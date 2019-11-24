<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment_model extends CI_Model {

	/**
	 * Get list of all jobtitle by its group id
	 * @param int $group
	 * @return array
	 */
	public function jobtitle_by_grade(int $grade) : array
	{
		$this->db->select('
			a.job_title_id, 
			a.section_id,
			a.grade,
			b.name as jobtitleName,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('sections c', 'c.id = a.section_id');
		$this->db->where('a.grade <=', $grade);
		$this->db->group_by('a.job_title_id, a.grade, a.section_id');
		$this->db->order_by('a.grade', 'asc');
		return $this->db->get()->result();
	}

	/**
	 * Get list of all jobtitle by group id and section id
	 * @param int $group
	 * @param int $section
	 * @return array
	 */
	public function jobtitle_by_grade_and_section(int $grade, int $section) : array
	{
		$this->db->select('
			a.job_title_id, 
			a.section_id,
			a.grade,
			b.name as jobtitleName,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('sections c', 'c.id = a.section_id');
		$this->db->where('a.grade <=', $grade);
		$this->db->where('a.section_id', $section);
		$this->db->group_by('a.job_title_id, a.grade');
		$this->db->order_by('a.grade, b.name', 'asc');
		return $this->db->get()->result();
	}
			

}

/* End of file Assessment.php */
/* Location: ./application/models/Assessment.php */