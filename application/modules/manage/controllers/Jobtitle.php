<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobtitle extends CI_Controller {

	private $group, $level, $grade, $section;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login_session')) {
			redirect(base_url('logout'));
		}

		$loginSession  = $this->session->userdata('login_session');
		$this->group   = $loginSession['group'];
		$this->level   = $loginSession['level'];
		$this->grade   = $loginSession['grade'];
		$this->section = $loginSession['section'];

		$this->load->model('manage_model','manage');
	}

	/**
	 * Get all job title list by section ID
	 * @param int $id
	 * @return void
	 */
	public function jobtitles(int $id) : void
	{
		$data['positions']   = $this->db->where('deleted_at')->get('positions')->result();
		$data['groups']      = $this->db->where('deleted_at')->get('job_groups')->result();
		$data['departments'] = $this->manage->get_all_departments();
		$data['sectionID']   = $id;
		$data['jobtitles']   = $this->manage->get_jobtitles_list($id);
		$data['page']        = 'jobtitle_v';
		$this->load->view('template/template', $data);
	}

	/**
	 * Update section from jobtite view
	 * 
	 * @return void
	 */
	public function update_section() : void
	{
		$sectionId  = $this->input->post('sectionID');
		$section    = $this->input->post('section');
		$department = $this->input->post('department');

		$object = ['name' => $section, 'dept_id' => $department];

		$this->db->where('id', $sectionId);
		$this->db->update('sections', $object);
		$this->session->set_flashdata('success_update_data', 'Update successfully!');

		redirect(base_url('section/'.$sectionId.'/jobtitle'));
	}
	
	/**
	 * Store or update job title
	 * 
	 * @return void
	 */
	public function store() : void
	{
		$isUpdate  = $this->input->post('isUpdate');
		$sectionID = $this->input->post('section_id');
		$jobtitle  = $this->input->post('jobtitle');
		$position  = $this->input->post('position');
		$group     = $this->input->post('group');

		$object = [
			'name'        => $jobtitle,
			'position_id' => $position,
			'section'     => $sectionID,
			'group'       => $group
		];

		if ($isUpdate == '') {
			$this->db->insert('job_titles', $object);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		} else {
			$this->db->where('id', $isUpdate);
			$this->db->update('job_titles', $object);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
		}

		redirect(base_url('section/'.$sectionID.'/jobtitle'));
	}

	/**
	 * Get detail job title
	 * @param int $id
	 * @return void
	 */
	public function detail(int $id) : void
	{
		$data = $this->db->where('id', $id)->get('job_titles',1)->row();
		$reponse = [
			'id'          => $data->id,
			'name'        => $data->name,
			'position_id' => $data->position_id,
			'group'       => $data->group
		];
		echo json_encode($data);
	}

	/**
	 * Remove job title. You must check to employes table before remove the job title.
	 * @param string $id. Because format of id parsed is [int]-[int].
	 * @return void
	 */
	public function remove(string $id) : void
	{
		$jobtitleID = explode('-', $id)[0];
		$sectionID  = explode('-', $id)[1];

		$this->_is_jobtitle_used($id);

		$this->db->delete('job_titles', ['id' => $jobtitleID]);
		$this->session->set_flashdata('success_remove_data', 'Remove successfully!');
		redirect(base_url('section/'.$sectionID.'/jobtitle'));
	}

	/**
	 * Check whether job title used by employee
	 * @param string $id.
	 * @return void
	 */
	private function _is_jobtitle_used(string $id) : void
	{
		$jobtitleID = explode('-', $id)[0];
		$sectionID  = explode('-', $id)[1];

		$number_of_uses = $this->db->get_where('employes', ['job_title_id' => $jobtitleID])->num_rows();
		if ($number_of_uses > 0) {
			$this->session->set_flashdata('fail_remove_data', 'Cannot remove! Job title has been used!');
			redirect(base_url('section/'.$sectionID.'/jobtitle'));
		}
		return;
	}

}

/* End of file Jobtitle.php */
/* Location: ./application/modules/manage/controllers/Jobtitle.php */