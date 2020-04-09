<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

	public $position, $position_grade, $nik;

	public function __construct()
	{
		parent::__construct();
		$loginSession = $this->session->userdata('login_session');
		$this->position       = $loginSession['position'];
		$this->position_grade = $loginSession['position_grade'];
		$this->nik            = $loginSession['nik'];
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
										AND position_id IN 
										(SELECT id FROM positions WHERE grade <= 3)");
				break;
			
			default:
				// for AM or SAM
				if ($this->position_grade > 3 && $this->position_grade < 7) {
					return $this->db->query("SELECT * FROM employes 
											WHERE name <> 'admin' 
											AND section_id = $section
											AND position_id IN 
											(SELECT id FROM positions WHERE grade <= 3)");
				// for MGR, DGM, GM 
				} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
					return $this->db->query("SELECT * FROM employes 
											WHERE name <> 'admin' 
											AND dept_id IN ($department)
											AND position_id IN 
											(SELECT id FROM positions WHERE grade <= 3)");
				// for DIR
				} elseif ($this->position_grade > 8) {
					return $this->db->query("SELECT * FROM employes 
											WHERE name <> 'admin' 
											AND position_id IN 
											(SELECT id FROM positions WHERE grade <= 3)");
				}
				break;
		}		
	}

	public function get_participants_by_head(int $nik=0) : object
	{	
		$heads = $this->get_head($nik);

		$this->db->select("em.*, dp.name AS dept_name, sc.name AS sect_name, jt.name AS job_name");
		$this->db->from("employee_relations emr");
		$this->db->join("employes em","emr.nik = em.nik");
		$this->db->join('departements dp', 'em.dept_id = dp.id');
		$this->db->join('sections sc', 'em.section_id = sc.id');
		$this->db->join('job_titles jt', 'em.job_title_id = jt.id');
		$this->db->where("em.grade <=","3");
		$this->db->where_in("emr.head", $heads);
		$all_team = $this->db->get();

		return $all_team;
	}

	public function get_head(int $nik=0) : array 
	{
		$head        = array($nik);
		$filter      = array();
		$filter_temp = array();

		$team 	= $this->db->query("SELECT emr.nik FROM employee_relations emr
									JOIN employes em ON emr.nik = em.nik  
									WHERE emr.head = ".$nik)->result();

		foreach ($team as $key => $value) {
			array_push($filter,$value->nik);
		}

		// 7 patokan kira2
		for ($i=0; $i < 5; $i++) {	
			$y = count($filter);
			for ($i=0; $i < $y ; $i++) { 
				
				$team 	= $this->db->query("SELECT emr.nik FROM employee_relations emr
											JOIN employes em ON emr.nik = em.nik 
											WHERE emr.head = ".$filter[$i])->result();

				if (count($team) > 0) {
					foreach ($team as $key => $value) {
						array_push($filter_temp,$value->nik);
					}
					array_push($head, $filter[$i]);
				}
			}
			
			if (count($filter_temp) == 0) {
				break;
			}

			$filter = $filter_temp;
			$filter_temp = array();
		}

		return $head;
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
															AND position_id IN (SELECT id FROM positions WHERE grade <= 3)"
														)->num_rows();

				$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
															WHERE code LIKE '%$activeYear' 
															AND code NOT IN 
															(SELECT code FROM assessment_form_state 
															WHERE code LIKE '%$activeYear')")->num_rows();
				break;
			
			default:
				// if AM or SAM
				if ($this->position_grade > 3 && $this->position_grade < 7) {
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
				} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
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
				} elseif ($this->position_grade > 8) {
					$employeHasntAssessed 	= $this->db->query("SELECT * FROM employes 
																WHERE nik NOT IN 
																(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
																AND name NOT LIKE '%admin%'
																AND position_id IN 
																(SELECT id FROM positions WHERE grade < 4)"
															)->num_rows();

					$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
																WHERE total_poin IS NULL 
																AND code LIKE '%$activeYear'"
															)->num_rows();

				}

				break;
		}

		return $uncompleteAssessment + $employeHasntAssessed; 
	}

	/**
	 * Get number of uncomplete assessment
	 * @param bool $notAdminOrHR
	 * @param int $section
	 * @return int
	 */
	public function uncomplete2(bool $notAdminOrHR=FALSE, int $sectOrDept=0) : int
	{
		$activeYear = get_active_year();

		switch ($notAdminOrHR) {
			case FALSE:
				$employeHasntAssessed 	= $this->db->query("SELECT * FROM employes 
															WHERE nik NOT IN 
																(SELECT nik FROM assessment_forms 
																WHERE code LIKE '%$activeYear%')
															AND name NOT LIKE '%admin%'
															AND position_id IN (SELECT id FROM positions WHERE grade <= 3)"
														)->num_rows();

				$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
															WHERE code LIKE '%$activeYear%' 
															AND total_poin IS NULL")->num_rows();
				break;
			
			default:
				$subquery    = "SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear%'";
				$participant = $this->get_participants_by_head($this->nik)->result();

				foreach ($participant as $key => $value) {
					$participants[] = $value->nik;
				}

				$employeHasntAssessed = $this->db->select('*')
												->from('employes')
												->where('nik NOT IN ('.$subquery.')', NULL, FALSE)
												->where_in('nik', $participants)
												->get()->num_rows();

				$uncompleteAssessment = $this->db->select('*')
												->from('assessment_forms')
												->where('total_poin')
												->where_in('nik', $participants)
												->get()->num_rows();

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
	public function complete2(bool $notAdminOrHR=FALSE, int $sectOrDept=0)
	{
		$activeYear = get_active_year();

		switch ($notAdminOrHR) {
			case FALSE:
				return $this->db->query("SELECT * FROM assessment_forms a 
										WHERE a.code LIKE '%$activeYear%'
										AND total_poin IS NOT NULL")->num_rows();
				break;
			
			default:
				$subquery    = "SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear%'";
				$participant = $this->get_participants_by_head($this->nik)->result();

				foreach ($participant as $key => $value) {
					$participants[] = $value->nik;
				}

				return $this->db->select('*')
								->from('assessment_forms')
								->like('code',$activeYear,'both')
								->where('total_poin IS NOT NULL', NULL, FALSE)
								->where_in('nik', $participants)
								->get()->num_rows();
				
				break;
		}
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
				if ($this->position_grade > 3 && $this->position_grade < 7) {
					return $this->db->query("SELECT * FROM assessment_forms
											WHERE code LIKE '%$activeYear'
											AND total_poin IS NOT NULL
											AND nik IN 
											(SELECT nik FROM employes where section_id = '$sectOrDept')")->num_rows();
				// if MGR and higher
				} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
					return $this->db->query("SELECT * FROM assessment_forms
											WHERE code LIKE '%$activeYear'
											AND total_poin IS NOT NULL
											AND nik IN 
											(SELECT nik FROM employes where dept_id = '$sectOrDept')")->num_rows();
				} elseif ($this->position_grade > 8) {
					return $this->db->query("SELECT * FROM assessment_forms
											WHERE code LIKE '%$activeYear'
											AND total_poin IS NOT NULL")->num_rows();
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
			return $this->db->query("SELECT 
										em.name, 
										dp.name AS dept_name,
										sc.name AS sect_name,
										jt.name AS job_name 
									FROM employes em
									JOIN departements dp ON em.dept_id = dp.id
									JOIN sections sc ON em.section_id = sc.id
									JOIN job_titles jt ON em.job_title_id = jt.id
									WHERE em.name <> 'admin' 
									AND em.position_id NOT IN 
									(SELECT id FROM positions where id > 6)")->result();
		} else {
			// assistant manager or senior assistant manager
			if ($this->position_grade > 3 && $this->position_grade < 7) {
				return $this->db->query("SELECT name, job_title_id FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where id > 6)
										AND section_id = $sectOrDept")->result();
			// assistant manager or senior assistant manager upper
			} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
				return $this->db->query("SELECT name, job_title_id FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where id > 6)
										AND dept_id = $sectOrDept")->result();
			} elseif ($this->position_grade > 8) {
				return $this->db->query("SELECT name, job_title_id FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where id > 6)
										AND dept_id IN ($sectOrDept)")->result();
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
			if ($this->position_grade > 3 && $this->position_grade < 7) {
				$fixArray = $this->_uncomplete_viewed_assistant_manager($activeYear, $sectOrDept);
			// for GM and higher
			} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
				$fixArray = $this->_uncomplete_viewed_manager($activeYear, $sectOrDept);
			} elseif ($this->position_grade > 8) {
				$fixArray = $this->_uncomplete_viewed_director($activeYear, $sectOrDept);
			}

			return $fixArray;
		}
		
	}

	/**
	 * Get employes whose uncomplete their assessment
	 * @param int $sectOrDept
	 * @return array
	 */
	public function uncomplete_employes2(bool $adminOrHR=TRUE, int $sectOrDept=0) : array
	{
		$activeYear = get_active_year();

		if ($adminOrHR) {
			$fixArray = $this->_uncomplete_viewed_admin($activeYear);
			return $fixArray;

		} else {
			$fixArray = $this->_uncomplete_view2($activeYear, $sectOrDept);
			return $fixArray;
		}
	}

	/**
	 * Uncomplete assessment form viewed by  GM and higher
	 * @param string $activeYear
	 * @param string $sectOrDept
	 * @return array
	 */
	private function _uncomplete_view2(string $activeYear, int $sectOrDept) : array
	{
		$subquery    = "SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear%'";
		$participant = $this->get_participants_by_head($this->nik)->result();

		foreach ($participant as $key => $value) {
			$participants[] = $value->nik;
		}

		$employeHasntAssessed = $this->db->select('em.*, dp.name AS dept_name, sc.name AS sect_name, jt.name AS job_name')
										->from('employes em')
										->join('departements dp', 'em.dept_id = dp.id')
										->join('sections sc', 'em.section_id = sc.id')
										->join('job_titles jt', 'em.job_title_id = jt.id')
										->where('nik NOT IN ('.$subquery.')', NULL, FALSE)
										->where_in('nik', $participants)
										->get()->result();

		$uncompleteAssessment = $this->db->select('a.*,b.name, dp.name AS dept_name, sc.name AS sect_name, jt.name AS job_name')
										->from('assessment_forms a')
										->join('employes b', 'a.nik = b.nik')
										->join('departements dp', 'b.dept_id = dp.id')
										->join('sections sc', 'b.section_id = sc.id')
										->join('job_titles jt', 'b.job_title_id = jt.id')
										->where('total_poin')
										->where_in('a.nik', $participants)
										->get()->result();
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
	 * Uncomplete assessment fomr viewed by admin or HR
	 * @param string $activeYear
	 * @return array
	 */
	private function _uncomplete_viewed_admin(string $activeYear) : array
	{
		$employeHasntAssessed 	= $this->db->query("SELECT 
														em.*, 
														dp.name AS dept_name, 
														sc.name AS sect_name,
														jt.name AS job_name 
													FROM employes em
													JOIN departements dp ON em.dept_id = dp.id
													JOIN sections sc ON em.section_id = sc.id
													JOIN job_titles jt ON em.job_title_id = jt.id
													WHERE em.nik NOT IN 
													(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear%')
													AND em.name NOT LIKE '%admin%'
													AND em.position_id IN (SELECT id FROM positions WHERE grade <= 3)"
												)->result();

		$uncompleteAssessment 	= $this->db->query("SELECT 
														a.*, 
														b.name, 
														dp.name AS dept_name, 
														sc.name AS sect_name,
														jt.name AS job_name
													FROM assessment_forms a
													JOIN employes b ON a.nik = b.nik
													JOIN departements dp ON em.dept_id = dp.id
													JOIN sections sc ON em.section_id = sc.id
													JOIN job_titles jt ON em.job_title_id = jt.id
													WHERE a.code LIKE '%$activeYear%' 
													AND a.total_poin IS NULL")->result();
		
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
	 * Uncomplete assessment form viewed by  GM and higher
	 * @param string $activeYear
	 * @param string $sectOrDept
	 * @return array
	 */
	private function _uncomplete_viewed_director(string $activeYear, int $sectOrDept) : array
	{
		$employeHasntAssessed 	= $this->db->query("SELECT name, job_title_id FROM employes 
													WHERE nik NOT IN 
													(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
													AND name NOT LIKE '%admin%'
													AND position_id NOT IN 
													(SELECT id FROM positions WHERE name LIKE '%manager')
													AND dept_id IN ($sectOrDept)")->result();

		$uncompleteAssessment 	= $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
													JOIN employes b ON a.nik = b.nik
													WHERE a.total_poin IS NULL 
													AND a.nik IN 
													(SELECT nik FROM employes where dept_id IN ($sectOrDept))
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

	public function jobtitle_chart(int $nik=0) : array
	{
		$heads = $this->get_head($nik);
		$this->db->select("jt.name as job_title, count(em.nik) as amount");
		$this->db->from("employee_relations emr");
		$this->db->join("employes em","emr.nik = em.nik");
		$this->db->join("job_titles jt","jt.id = em.job_title_id","left");
		$this->db->where("grade <=","3");
		// $this->db->where('jt.name IS NOT NULL', NULL, FALSE);
		$this->db->where_in("emr.head", $heads);
		$this->db->group_by("em.job_title_id");
		$all_team = $this->db->get()->result();
		return $all_team;
	}

	/**
	 * Get number of employes in each job title
	 * @param bool $adminOrHR
	 * @param int $sectOrDept
	 * @return array
	 */
	public function employe_per_jobtitle(bool $adminOrHR=true, int $sectOrDept=0) : array
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
			if ($this->position_grade > 3 && $this->position_grade < 7) {
				return $this->db->query("SELECT 
											a.name AS job_title, 
											count(b.nik) AS amount 
										FROM job_titles a JOIN employes b ON a.id = b.job_title_id
										WHERE b.name <> 'admin'
										AND b.section_id = $sectOrDept
										GROUP BY b.job_title_id")->result();
			// if DGM or higher
			} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
				return $this->db->query("SELECT 
											a.name AS job_title, 
											count(b.nik) AS amount 
										FROM job_titles a JOIN employes b ON a.id = b.job_title_id
										WHERE b.name <> 'admin'
										AND b.dept_id = $sectOrDept
										GROUP BY b.job_title_id")->result();
			// if director
			} elseif ($this->position_grade > 8) {
				return $this->db->query("SELECT 
											a.name AS job_title, 
											count(b.nik) AS amount 
										FROM job_titles a JOIN employes b ON a.id = b.job_title_id
										WHERE b.name <> 'admin'
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
	public function employe_per_grade2(bool $adminOrHR=true, int $sectOrDept=0) : array
	{
		if ($adminOrHR) {
			return $this->db->query("SELECT 
										grade AS level, 
										count(nik) AS amount 
									FROM employes
									WHERE name <> 'admin'
									AND grade < 4
									GROUP BY grade")->result();
		} else {
			$participant = $this->get_participants_by_head($this->nik)->result();

			foreach ($participant as $key => $value) {
				$participants[] = $value->nik;
			}

			return $this->db->select('grade AS level, count(nik) AS amount ')
							->from('employes')
							->where('name !=', 'admin')
							->where_in('nik', $participants)
							->where('grade <', 4)
							->group_by('grade')
							->get()->result();
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
			if ($this->position_grade > 3 && $this->position_grade < 7) {
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
			} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
				return $this->db->query("SELECT 
											grade AS level, 
											count(nik) AS amount 
										FROM employes
										WHERE dept_id = $sectOrDept
										AND name <> 'admin'
										AND position_id NOT IN
										(SELECT id FROM positions where id > 6)
										GROUP BY grade")->result();
			// if director
			} elseif ($this->position_grade > 8) {
				return $this->db->query("SELECT 
											grade AS level, 
											count(nik) AS amount 
										FROM employes
										WHERE name <> 'admin'
										AND position_id NOT IN
										(SELECT id FROM positions where grade > 3)
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
			if ($this->position_grade > 3 && $this->position_grade < 7) {
				return $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
										JOIN employes b ON a.nik = b.nik
										WHERE code LIKE '%$activeYear'
										AND total_poin IS NOT NULL
										AND a.nik IN 
										(SELECT nik FROM employes where section_id = '$sectOrDept')")->result();
			// for GM and higher
			} elseif ($this->position_grade > 6 && $this->position_grade < 9) {
				return $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
										JOIN employes b ON a.nik = b.nik
										WHERE code LIKE '%$activeYear'
										AND total_poin IS NOT NULL
										AND a.nik IN 
										(SELECT nik FROM employes where dept_id = '$sectOrDept')")->result();
			} elseif ($this->position_grade > 8) {
				return $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
										JOIN employes b ON a.nik = b.nik
										WHERE code LIKE '%$activeYear'
										AND total_poin IS NOT NULL
										AND a.nik IN 
										(SELECT nik FROM employes where dept_id IN ($sectOrDept) )")->result();
			}
		}
	}

	/**
	 * Get number of completed assessment
	 * @param bool $notAdminOrHR
	 * @param int $sectOrDept
	 * @return int
	 */
	public function complete_detail2(bool $notAdminOrHR=FALSE, int $sectOrDept=0)
	{
		$activeYear = get_active_year();

		switch ($notAdminOrHR) {
			case FALSE:
				return $this->db->query("SELECT 
											em.*,
											dp.name AS dept_name, 
											sc.name AS sect_name,
											jt.name AS job_name 
										FROM assessment_forms af 
										JOIN employes em ON af.nik = em.nik
										JOIN departements dp ON em.dept_id = dp.id
										JOIN sections sc ON em.section_id = sc.id
										JOIN job_titles jt ON em.job_title_id = jt.id
										WHERE af.code LIKE '%$activeYear%'
										AND total_poin IS NOT NULL")->result();
				break;
			
			default:
				$subquery    = "SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear%'";
				$participant = $this->get_participants_by_head($this->nik)->result();

				foreach ($participant as $key => $value) {
					$participants[] = $value->nik;
				}

				return $this->db->select('em.*, dp.name AS dept_name, sc.name AS sect_name, jt.name AS job_name')
								->from('assessment_forms af')
								->join('employes em','af.nik = em.nik')
								->join('departements dp', 'em.dept_id = dp.id')
								->join('sections sc', 'em.section_id = sc.id')
								->join('job_titles jt', 'em.job_title_id = jt.id')
								->like('code',$activeYear,'both')
								->where('total_poin IS NOT NULL', NULL, FALSE)
								->where_in('af.nik', $participants)
								->get()->result();
				
				break;
		}
	}
}

/* End of file Dashboard_model.php */
/* Location: ./application/models/Dashboard_model.php */