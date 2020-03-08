<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

	public $position;

	public function __construct()
	{
		parent::__construct();
		$loginSession = $this->session->userdata('login_session');
		$this->position = $loginSession['position'];
	}

	/**
	 * Get assesment participants. If user login not as admin or HR, use section id to filter it.
	 * @param int $section
	 * @return object
	 */
	public function get_participants(int $section=0, $department='') : object
	{
		switch ($section) {
			case 0:
				return $this->db->query("SELECT * FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where name > 6)");
				break;
			
			default:
				// login as upper assistant manager
				if (!empty($department)) {
					return $this->db->query("SELECT * FROM employes 
											WHERE name <> 'admin' 
											AND dept_id = $department
											AND position_id NOT IN 
											(SELECT id FROM positions where id > 6)");
				// login as manager and upper
				} else {
					return $this->db->query("SELECT * FROM employes 
											WHERE name <> 'admin' 
											AND section_id = $section
											AND position_id NOT IN 
											(SELECT id FROM positions where id > 6)");	
				}
				break;
		}		
	}

	/**
	 * Get number of uncomplete assessment
	 * @param bool $notAdminOrHR
	 * @param int $section
	 * @return int
	 */
	public function uncomplete(bool $notAdminOrHR=FALSE, int $sectOrDept=0) : int
	{
		$activeYear = get_active_year();

		switch ($notAdminOrHR) {
			case FALSE:
				$employeHasntAssessed 	= $this->db->query("SELECT * FROM employes 
															WHERE nik NOT IN 
															(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
															AND name NOT LIKE '%admin%'
															AND position_id NOT IN 
															(SELECT id FROM positions WHERE name LIKE '%manager')"
														)->num_rows();

				$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
															WHERE code LIKE '%$activeYear' 
															AND code NOT IN 
															(SELECT code FROM assessment_validations 
															WHERE code LIKE '%$activeYear')")->num_rows();
				break;
			
			default:
				// if AM or SAM
				if ($this->position == 7 || $this->position == 8) {
					$employeHasntAssessed 	= $this->db->query("SELECT * FROM employes 
																WHERE nik NOT IN 
																(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
																AND name NOT LIKE '%admin%'
																AND position_id NOT IN 
																(SELECT id FROM positions WHERE name LIKE '%manager')
																AND section_id = '$sectOrDept'"
															)->num_rows();

					$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
																WHERE total_poin IS NULL 
																AND nik IN 
																(SELECT nik FROM employes where section_id = '$sectOrDept')
																AND code LIKE '%$activeYear'"
															)->num_rows();
				// if GM and higher
				} else {
					$employeHasntAssessed 	= $this->db->query("SELECT * FROM employes 
																WHERE nik NOT IN 
																(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
																AND name NOT LIKE '%admin%'
																AND position_id NOT IN 
																(SELECT id FROM positions WHERE name LIKE '%manager')
																AND dept_id = '$sectOrDept'"
															)->num_rows();

					$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
																WHERE total_poin IS NULL 
																AND nik IN 
																	(SELECT nik FROM employes 
																	WHERE dept_id = '$sectOrDept')
																AND code LIKE '%$activeYear'"
															)->num_rows();
				}

				break;
		}

		return $uncompleteAssessment + $employeHasntAssessed; 
	}

	/**
	 * Get number of completed assessment
	 * @param bool $notAdminOrHR
	 * @param int $sectOrDept
	 * @return int
	 */
	public function complete(bool $notAdminOrHR=FALSE, int $sectOrDept=0)
	{
		$activeYear = get_active_year();

		switch ($notAdminOrHR) {
			case FALSE:
				return $this->db->query("SELECT * FROM assessment_forms a 
										JOIN assessment_validations b ON a.code = b.code 
										WHERE a.code LIKE '%$activeYear'")->num_rows();
				break;
			
			default:
				// if AM or SAM
				if ($this->position == 7 || $this->position == 8) {
					return $this->db->query("SELECT * FROM assessment_forms
											WHERE code LIKE '%$activeYear'
											AND total_poin IS NOT NULL
											AND nik IN 
											(SELECT nik FROM employes where section_id = '$sectOrDept')")->num_rows();
				// if MGR and higher
				} else {
					return $this->db->query("SELECT * FROM assessment_forms
											WHERE code LIKE '%$activeYear'
											AND total_poin IS NOT NULL
											AND nik IN 
											(SELECT nik FROM employes where dept_id = '$sectOrDept')")->num_rows();
				}
				
				break;
		}
	}

	/**
	 * Get detail participant
	 * @param int $sectOrDept
	 * @return array
	 */
	public function get_participants_detail(int $sectOrDept=0) : array
	{
		if ($sectOrDept == 0) {
			return $this->db->query("SELECT name, job_title_id FROM employes 
									WHERE name <> 'admin' 
									AND position_id NOT IN 
									(SELECT id FROM positions where id > 6)")->result();
		} else {
			// assistant manager or senior assistant manager
			if ($this->position == 7 || $this->position == 8) {
				return $this->db->query("SELECT name, job_title_id FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where id > 6)
										AND section_id = $sectOrDept")->result();
			// assistant manager or senior assistant manager upper
			} elseif ($this->position > 8) {
				return $this->db->query("SELECT name, job_title_id FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where id > 6)
										AND dept_id = $sectOrDept")->result();
			}
		}
	}
	
	
	/**
	 * Get employes whose uncomplete their assessment
	 * @param int $sectOrDept
	 * @return array
	 */
	public function uncomplete_employes(bool $adminOrHR=TRUE, int $sectOrDept=0) : array
	{
		$activeYear = get_active_year();

		if ($adminOrHR) {
			$fixArray = $this->_uncomplete_viewed_admin();
			return $fixArray;

		} else {
			// for AM or SAM
			if ($this->position == 7 || $this->position == 8) {
				$fixArray = $this->_uncomplete_viewed_assistant_manager($activeYear, $sectOrDept);
			// for GM and higher
			} else {
				$fixArray = $this->_uncomplete_viewed_manager($activeYear, $sectOrDept);
			}

			return $fixArray;
		}
		
	}

	/**
	 * Uncomplete assessment fomr viewed by admin or HR
	 * @param string $activeYear
	 * @return array
	 */
	private function _uncomplete_viewed_admin(string $activeYear) : array
	{
		$employeHasntAssessed 	= $this->db->query("SELECT name, job_title_id FROM employes 
													WHERE nik NOT IN 
													(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
													AND name NOT LIKE '%admin%'
													AND position_id NOT IN 
													(SELECT id FROM positions WHERE name LIKE '%manager')"
													)->result();

		$uncompleteAssessment 	= $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
													JOIN employes b ON a.nik = b.nik
													WHERE code LIKE '%$activeYear' 
													AND code NOT IN 
													(SELECT code FROM assessment_validations 
													WHERE code LIKE '%$activeYear')")->result();
		
		$fixArray = [];

		foreach ($employeHasntAssessed as $employe => $value) {
			array_push($fixArray,$value);
		}

		foreach ($uncompleteAssessment as $employe => $value) {
			array_push($fixArray,$value);
		}

		return $fixArray;
	}

	/**
	 * Uncomplete assessment form viewed by  GM and higher
	 * @param string $activeYear
	 * @param string $sectOrDept
	 * @return array
	 */
	private function _uncomplete_viewed_assistant_manager(string $activeYear, int $sectOrDept) : array
	{
		$employeHasntAssessed 	= $this->db->query("SELECT name, job_title_id FROM employes 
													WHERE nik NOT IN 
													(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
													AND name NOT LIKE '%admin%'
													AND position_id NOT IN 
													(SELECT id FROM positions WHERE name LIKE '%manager')
													AND section_id = '$sectOrDept'")->result();

		$uncompleteAssessment 	= $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
													JOIN employes b ON a.nik = b.nik
													WHERE a.total_poin IS NULL 
													AND a.nik IN 
													(SELECT nik FROM employes where section_id = '$sectOrDept')
													AND a.code LIKE '%$activeYear'")->result();
		$fixArray = [];

		foreach ($employeHasntAssessed as $employe => $value) {
			array_push($fixArray,$value);
		}

		foreach ($uncompleteAssessment as $employe => $value) {
			array_push($fixArray,$value);
		}
		return $fixArray;
	}

	/**
	 * Uncomplete assessment form viewed by  GM and higher
	 * @param string $activeYear
	 * @param string $sectOrDept
	 * @return array
	 */
	private function _uncomplete_viewed_manager(string $activeYear, int $sectOrDept) : array
	{
		$employeHasntAssessed 	= $this->db->query("SELECT name, job_title_id FROM employes 
													WHERE nik NOT IN 
													(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
													AND name NOT LIKE '%admin%'
													AND position_id NOT IN 
													(SELECT id FROM positions WHERE name LIKE '%manager')
													AND dept_id = '$sectOrDept'")->result();

		$uncompleteAssessment 	= $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
													JOIN employes b ON a.nik = b.nik
													WHERE a.total_poin IS NULL 
													AND a.nik IN 
													(SELECT nik FROM employes where dept_id = '$sectOrDept')
													AND a.code LIKE '%$activeYear'")->result();
		$fixArray = [];

		foreach ($employeHasntAssessed as $employe => $value) {
			array_push($fixArray,$value);
		}

		foreach ($uncompleteAssessment as $employe => $value) {
			array_push($fixArray,$value);
		}

		return $fixArray;
	}

	/**
	 * Get number of employes in each job title
	 * @param bool $adminOrHR
	 * @param int $section
	 * @return array
	 */
	public function employe_per_jobtitle(bool $adminOrHR=true, int $section=0, int $positions) : array
	{
		if ($adminOrHR) {
			return $this->db->query("SELECT 
										a.name AS job_title, 
										count(b.nik) AS amount 
									FROM job_titles a JOIN employes b ON a.id = b.job_title_id
									WHERE b.name <> 'admin'
									GROUP BY b.job_title_id")->result();
		} else {
			// if AM OR SAM
			if ($positions == 7 || $positions == 8) {
				return $this->db->query("SELECT 
											a.name AS job_title, 
											count(b.nik) AS amount 
										FROM job_titles a JOIN employes b ON a.id = b.job_title_id
										WHERE b.name <> 'admin'
										AND b.section_id = $section
										GROUP BY b.job_title_id")->result();
			// if MGR or higher
			} else {
				return $this->db->query("SELECT 
											a.name AS job_title, 
											count(b.nik) AS amount 
										FROM job_titles a JOIN employes b ON a.id = b.job_title_id
										WHERE b.name <> 'admin'
										AND b.dept_id = $section
										GROUP BY b.job_title_id")->result();
			}
		}
	}

	/**
	 * Get number of employes in each job title
	 * @param bool $adminOrHR
	 * @param int $sectOrDept
	 * @return array
	 */
	public function employe_per_grade(bool $adminOrHR=true, int $sectOrDept=0) : array
	{
		if ($adminOrHR) {
			return $this->db->query("SELECT 
										grade AS level, 
										count(nik) AS amount 
									FROM employes
									WHERE name <> 'admin'
									GROUP BY grade")->result();
		} else {
			// if AM or SAM
			if ($this->position == 7 || $this->position == 8) {
				return $this->db->query("SELECT 
											grade AS level, 
											count(nik) AS amount 
										FROM employes
										WHERE section_id = $sectOrDept
										AND name <> 'admin'
										AND position_id NOT IN
										(SELECT id FROM positions where id > 6)
										GROUP BY grade")->result();
			// if GM and higher
			} elseif ($this->position > 8) {
				return $this->db->query("SELECT 
											grade AS level, 
											count(nik) AS amount 
										FROM employes
										WHERE dept_id = $sectOrDept
										AND name <> 'admin'
										AND position_id NOT IN
										(SELECT id FROM positions where id > 6)
										GROUP BY grade")->result();
			}
		}
	}

	/**
	 * Get employes whose complete assessment
	 * @param bool $adminOrHR
	 * @param int $sectOrDept
	 * @return array
	 */
	public function complete_detail(bool $adminOrHR=TRUE, int $sectOrDept=0) : array
	{
		$activeYear = get_active_year();

		if ($adminOrHR) {
			return $this->db->query("SELECT c.name, c.job_title_id FROM assessment_forms a 
									JOIN assessment_validations b ON a.code = b.code 
									JOIN employes c ON c.nik = a.nik
									WHERE a.code LIKE '%$activeYear'")->result();
		} else {
			// for AM or SAM
			if ($this->position == 7 || $this->position == 8) {
				return $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
										JOIN employes b ON a.nik = b.nik
										WHERE code LIKE '%$activeYear'
										AND total_poin IS NOT NULL
										AND a.nik IN 
										(SELECT nik FROM employes where section_id = '$sectOrDept')")->result();
			// for GM and higher
			} else {
				return $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
										JOIN employes b ON a.nik = b.nik
										WHERE code LIKE '%$activeYear'
										AND total_poin IS NOT NULL
										AND a.nik IN 
										(SELECT nik FROM employes where dept_id = '$sectOrDept')")->result();
			}
				
		}
	}
	
}

/* End of file Dashboard_model.php */
/* Location: ./application/models/Dashboard_model.php */