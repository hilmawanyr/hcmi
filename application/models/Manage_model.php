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
}

/* End of file Manage_model.php */
/* Location: ./application/models/Manage_model.php */