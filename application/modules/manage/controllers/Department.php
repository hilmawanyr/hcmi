<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends CI_Controller {

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

	public function index()
	{
		$data['departments'] = $this->manage->get_all_departments();
		$data['page']        = 'department_v';
		$this->load->view('template/template', $data);
	}

	/**
	 * Store or update department
	 * 
	 * @return void
	 */
	public function store() : void
	{
		$isUpdate = $this->input->post('isUpdate');
		$name     = $this->input->post('name');

		// if $updateFromSectionView != 0, redirect it to section view
		$updateFromSectionView = $this->input->post('updateFromSection');

		// check is name has exist?
		$this->_is_name_exist($name);

		$storedData = ['name' => $name];

		if ($isUpdate == '') {
			$this->db->insert('departements', $storedData);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		} else {
			$this->db->where('id', $isUpdate)->update('departements', $storedData);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
		}

		if ($updateFromSectionView == 1) {
			redirect(base_url('department/'.$isUpdate.'/section'));
		}
		redirect(base_url('department'));
	}

	/**
	 * Check is department name has exist
	 * @param string $name
	 * @return void
	 */
	private function _is_name_exist(string $name) : void
	{
		$check = $this->db->where('name', $name)->get('departements')->num_rows();
		if ($check > 0) {
			$this->session->set_flashdata('fail_save_data', 'Can not save department. Department name has exist!');
			redirect(base_url('department'));
		}
		return;
	}

	/**
	 * Get detail department
	 * @param int $id
	 * @return void
	 */
	public function detail(int $id) : void
	{
		$department = $this->db->where('id', $id)->get('departements')->row();
		$response = [
			'id' => $id,
			'name' => $department->name
		];

		echo json_encode($response);
	}

}

/* End of file Department.php */
/* Location: ./application/modules/manage/controllers/Department.php */