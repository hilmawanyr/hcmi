<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage_model extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get all assessment year list
	 * 
	 * @return array
	 */
	public function get_assessment_year() : array
	{
		return $this->db->get('assessment_years')->result();
	}

	/**
	 * Get specify assessment year data
	 * @param int $id
	 * @return object
	 */
	public function get_assessment_year_detail(int $id) : object
	{
		$this->db->where('id', $id);
		return $this->db->get('assessment_years', 1)->row();
	}

	/**
	 * Get all departments
	 * 
	 * @return array
	 */
	public function get_all_departments() : array
	{
		$this->db->where('deleted_at');
		return $this->db->get('departements')->result();
	}

	/**
	 * Get all departments
	 * 
	 * @return array
	 */
	public function get_all_sections() : array
	{
		$this->db->where('deleted_at');
		$this->db->order_by('name');
		return $this->db->get('sections')->result();
	}

	/**
	 * Get section by department
	 * @param int $id
	 * @return array
	 */
	public function section_by_department(int $id) : array
	{
		$this->db->where('deleted_at');
		$this->db->where('dept_id', $id);
		return $this->db->get('sections')->result();
	}

	/**
	 * Get all jobtitles list
	 * @param int $id
	 * @return array
	 */
	public function get_jobtitles_list(int $id) : array
	{
		$this->db->where('section', $id);
		$this->db->order_by('name');
		return $this->db->get('job_titles')->result();
	}

	public function get_jobtitle(int $id) : object
	{
		$this->db->where('id', $id);
		return $this->db->get('job_titles');
	}

	/**
	 * Get specific or all information from informations table
	 * @param int $id; default null
	 * @return object
	 */
	public function get_information(int $id = null) : object
	{
		switch ($id) {
			case null:
				return $this->db->where('deleted_at')->get('informations');
				break;
			
			default:
				return $this->db->where('id', $id)->get('informations');
				break;
		}		
	}

	/**
	 * Get specific type information
	 * @param string $type
	 * @param string $orderBy
	 * @param int $limit
	 * @return object
	 */
	public function get_specific_type_information(string $type, string $orderBy = NULL, string $limit = NULL) : object
	{
		$this->db->where('type', $type);
		$this->db->where('deleted_at');

		if (!is_null($orderBy)) {
			$this->db->order_by('id', $orderBy);
		}

		if (!is_null($limit)) {
			$informations = $this->db->get('informations', $limit);
		} else {
			$informations = $this->db->get('informations');
		}

		return $informations;
	}

	/**
	 * Get all employes
	 * 
	 * @return object
	 */
	public function get_employes() : object
	{
		$this->db->select('a.*, b.name as section, c.name as position, d.name as jobtitle');
		$this->db->from('employes a');
		$this->db->join('sections b', 'a.section_id = b.id', 'left');
		$this->db->join('positions c', 'a.position_id = c.id', 'left');
		$this->db->join('job_titles d', 'a.job_title_id = d.id', 'left');
		return $this->db->get();
	}

	/**
	 * Get list of users
	 * 
	 * @return array
	 */
	public function get_users() : array
	{
		$this->db->select('a.*, b.name, c.name as group_name');
		$this->db->from('users a');
		$this->db->join('employes b', 'a.nik = b.nik', 'left');
		$this->db->join('groups c', 'c.id = a.group_id', 'left');
		return $this->db->get()->result();
	}

	/**
	 * Get user detail
	 * @param string $nik
	 * @return array
	 */
	public function get_detail_users(string $nik) : object
	{
		$this->db->select('a.*, b.name, c.name as group_name');
		$this->db->from('users a');
		$this->db->join('employes b', 'a.nik = b.nik', 'left');
		$this->db->join('groups c', 'c.id = a.group_id', 'left');
		$this->db->where('a.nik', $nik);
		return $this->db->get()->row();
	}
	
}

/* End of file Manage_model.php */
/* Location: ./application/models/Manage_model.php */