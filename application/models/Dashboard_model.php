<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get assesment participants. If user login not as admin or HR, use section id to filter it.
	 * @param int $section
	 * @return object
	 */
	public function get_participants(int $section=0) : object
	{
		switch ($section) {
			case 0:
				return $this->db->query("SELECT * FROM employes 
										WHERE name <> 'admin' 
										AND position_id NOT IN 
										(SELECT id FROM positions where name LIKE '%manager%')");
				break;
			
			default:
				return $this->db->query("SELECT * FROM employes 
										WHERE name <> 'admin' 
										AND section_id = $section
										AND position_id NOT IN 
										(SELECT id FROM positions where name LIKE '%manager%')");
				break;
		}		
	}

	/**
	 * Get number of uncomplete assessment
	 * @param bool $notAdminOrHR
	 * @param int $section
	 * @return int
	 */
	public function uncomplete(bool $notAdminOrHR=FALSE, int $section=0) : int
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
				$employeHasntAssessed 	= $this->db->query("SELECT * FROM employes 
															WHERE nik NOT IN 
															(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
															AND name NOT LIKE '%admin%'
															AND position_id NOT IN 
															(SELECT id FROM positions WHERE name LIKE '%manager')
															AND section_id = '$section'"
														)->num_rows();

				$uncompleteAssessment 	= $this->db->query("SELECT * FROM assessment_forms 
															WHERE total_poin IS NULL 
															AND nik IN 
															(SELECT nik FROM employes where section_id = '$section')
															AND code LIKE '%$activeYear'"
														)->num_rows();
				break;
		}

		return $uncompleteAssessment + $employeHasntAssessed; 
	}

	/**
	 * Get number of completed assessment
	 * @param bool $notAdminOrHR
	 * @param int $section
	 * @return int
	 */
	public function complete(bool $notAdminOrHR=FALSE, int $section=0)
	{
		$activeYear = get_active_year();

		switch ($notAdminOrHR) {
			case FALSE:
				return $this->db->query("SELECT * FROM assessment_forms a 
										JOIN assessment_validations b ON a.code = b.code 
										WHERE a.code LIKE '%$activeYear'")->num_rows();
				break;
			
			default:
				return $this->db->query("SELECT * FROM assessment_forms
										WHERE code LIKE '%$activeYear'
										AND total_poin IS NOT NULL
										AND nik IN 
										(SELECT nik FROM employes where section_id = '$section')")->num_rows();
				break;
		}
	}

	/**
	 * Get detail participant
	 * @param int $section
	 * @return array
	 */
	public function get_participants_detail(int $section=0) : array
	{
		if ($section == 0) {
			return $this->db->query("SELECT name, job_title_id FROM employes 
									WHERE name <> 'admin' 
									AND position_id NOT IN 
									(SELECT id FROM positions where name LIKE '%manager%')")->result();
		} else {
			return $this->db->query("SELECT name, job_title_id FROM employes 
									WHERE name <> 'admin' 
									AND position_id NOT IN 
									(SELECT id FROM positions where name LIKE '%manager%')
									AND section_id = $section")->result();
		}
	}
	
	
	/**
	 * Get employes whose uncomplete their assessment
	 * @param int $section
	 * @return array
	 */
	public function uncomplete_employes(bool $notAdminOrHR=TRUE, int $section=0) : array
	{
		$activeYear = get_active_year();

		if ($notAdminOrHR) {
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

		} else {
			$employeHasntAssessed 	= $this->db->query("SELECT name, job_title_id FROM employes 
														WHERE nik NOT IN 
														(SELECT nik FROM assessment_forms WHERE code LIKE '%$activeYear')
														AND name NOT LIKE '%admin%'
														AND position_id NOT IN 
														(SELECT id FROM positions WHERE name LIKE '%manager')
														AND section_id = '$section'")->result();

			$uncompleteAssessment 	= $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
														JOIN employes b ON a.nik = b.nik
														WHERE a.total_poin IS NULL 
														AND a.nik IN 
														(SELECT nik FROM employes where section_id = '$section')
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
		
	}

	/**
	 * Get number of employes in each job title
	 * @param bool $adminOrHR
	 * @param int $section
	 * @return array
	 */
	public function employe_per_jobtitle(bool $adminOrHR=true, int $section=0) : array
	{
		if ($adminOrHR) {
			return $this->db->query("SELECT 
										a.name AS job_title, 
										count(b.nik) AS amount 
									FROM job_titles a JOIN employes b ON a.id = b.job_title_id
									GROUP BY b.job_title_id")->result();
		} else {
			return $this->db->query("SELECT 
										a.name AS job_title, 
										count(b.nik) AS amount 
									FROM job_titles a JOIN employes b ON a.id = b.job_title_id
									WHERE b.section_id = $section
									GROUP BY b.job_title_id")->result();
		}
	}

	/**
	 * Get employes whose complete assessment
	 * @param bool $adminOrHR
	 * @param int $section
	 * @return array
	 */
	public function complete_detail(bool $adminOrHR=TRUE, int $section=0) : array
	{
		$activeYear = get_active_year();

		if ($adminOrHR) {
			return $this->db->query("SELECT c.name, c.job_title_id FROM assessment_forms a 
									JOIN assessment_validations b ON a.code = b.code 
									JOIN employes c ON c.nik = a.nik
									WHERE a.code LIKE '%$activeYear'")->result();
		} else {
			return $this->db->query("SELECT b.name, b.job_title_id FROM assessment_forms a
									JOIN employes b ON a.nik = b.nik
									WHERE code LIKE '%$activeYear'
									AND total_poin IS NOT NULL
									AND a.nik IN 
									(SELECT nik FROM employes where section_id = '$section')")->result();	
		}
	}
	
}

/* End of file Dashboard_model.php */
/* Location: ./application/models/Dashboard_model.php */