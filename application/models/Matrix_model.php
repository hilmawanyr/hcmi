<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Matrix_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get list of all job title with its department, section, position, and grade
	 * 
	 * @return object
	 */
	public function get_jobtitle(int $jobtitle=0) : object
	{
		if ($jobtitle == 0) {
			return $this->db->query("SELECT 
										a.id as jobtitle_id,
										a.name as jobtitle_name,
										a.group as jobtitle_group,
										b.id as position_id,
										b.name as position_name,
										b.grade as position_grade,
										c.id as section_id,
										c.name as section_name,
										d.id as department_id,
										d.name as department_name
									FROM job_titles a 
									JOIN positions b ON a.position_id = b.id 
									JOIN sections c ON c.id = a.section 
									LEFT JOIN departements d ON d.id = c.dept_id 
									WHERE b.deleted_at IS NULL 
									AND c.deleted_at IS NULL 
									AND d.deleted_at IS NULL");
		} else {
			return $this->db->query("SELECT 
										a.id as jobtitle_id,
										a.name as jobtitle_name,
										a.group as jobtitle_group,
										b.id as position_id,
										b.name as position_name,
										b.grade as position_grade,
										c.id as section_id,
										c.name as section_name,
										d.id as department_id,
										d.name as department_name
									FROM job_titles a 
									JOIN positions b ON a.position_id = b.id 
									JOIN sections c ON c.id = a.section 
									LEFT JOIN departements d ON d.id = c.dept_id 
									WHERE b.deleted_at IS NULL 
									AND c.deleted_at IS NULL 
									AND d.deleted_at IS NULL
									AND a.id  = '".$jobtitle."'")->row();
			
		}
	}

	/**
	 * Get competency matrix for each job title
	 * @param int $jobtitle
	 * @return array
	 */
	public function get_competency_matrix(int $jobtitle) : array
	{
		return $this->db->query("SELECT 
									a.id as matrix_id, 
									a.level, 
									b.name_id as name, 
									b.description 
								FROM skill_matrix a 
								JOIN skill_dictionaries b ON a.skill_id = b.id
								WHERE a.job_id = $jobtitle
								AND a.deleted_at IS NULL
								AND b.deleted_at IS NULL
								ORDER BY a.level ASC")->result();		
	}

	/**
	 * Get competency
	 * @param int $jobtitle
	 * @return array
	 */
	public function get_competency(int $jobtitle) : array
	{
		return $this->db->query("SELECT * FROM skill_dictionaries 
								WHERE deleted_at IS NULL 
								AND id NOT IN 
								(SELECT skill_id FROM skill_matrix WHERE job_id = $jobtitle AND deleted_at IS NULL)"
								)->result();		
	}
}

/* End of file Matrix_model.php */
/* Location: ./application/models/Matrix_model.php */