<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dictionary_competency extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Get all skill types
	 * 
	 * @return array
	 */
	public function get_skill_types() : array
	{
		$this->db->select('id, name');
		$this->db->where('deleted_at');
		return $this->db->get('skill_types')->result();
	}

	/**
	 * Get dictionary competency depend skill type id
	 * @param int $skillTypeId
	 * @return array
	 */
	public function get_dictionary_competency(int $skillTypeId) : array
	{
		$this->db->where('skill_group', $skillTypeId);
		$this->db->where('deleted_at');
		return $this->db->get('skill_dictionaries')->result();
	}

	/**
	 * Get detail competency dictionary
	 * @param int $dictionaryId
	 * @return object
	 */
	public function get_dictionary_detail(int $dictionaryId) : object
	{
		$this->db->where('id', $dictionaryId);
		$this->db->where('deleted_at');
		return $this->db->get('skill_dictionaries')->row();
	}

	/**
	 * Get detail competency dictionary by name
	 * @param string $name
	 * @return object
	 */
	public function get_dictionary_by_name(string $name) : object
	{
		$this->db->where('name_id', $name);
		$this->db->where_or('name_en', $name);
		$this->db->where('deleted_at');
		return $this->db->get('skill_dictionaries')->row();
	}

	/**
	 * Get skill unit by skill dictioanry id
	 * @param int $dictioanryId
	 * @return array
	 */
	public function skill_unit_by_dictionary(int $dictionaryId) : array
	{
		$this->db->where('id_dictionary', $dictionaryId);
		$this->db->where('deleted_at');
		return $this->db->get('skill_units')->result();
	}

	/**
	 * Get skill unit detail
	 * @param int $skillId
	 * @return array
	 */
	public function get_skill_unit(int $skillId) : array
	{
		$this->db->where('id', $skillId);
		$this->db->where('deleted_at');
		return $this->db->get('skill_units')->result();
	}
}

/* End of file Dictionary_competency.php */
/* Location: ./application/models/Dictionary_competency.php */