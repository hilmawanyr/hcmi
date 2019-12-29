<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Section extends CI_Controller {

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
	 * Get sections list by department ID
	 * @param int $id
	 * @return void
	 */
	public function sections(int $id) : void
	{
		$data['departmentID'] = $id;
		$data['sections']     = $this->manage->section_by_department($id);
		$data['page']         = 'section_v';
		$this->load->view('template/template', $data);		
	}

	/**
	 * Store or update section
	 * 
	 * @return void
	 */
	public function store() : void
	{
		$departmentID = $this->input->post('department_id');
		$isUpdate     = $this->input->post('isUpdate');
		$name         = $this->input->post('section');

		$section = ['name' => $name, 'dept_id' => $departmentID];

		if ($isUpdate == '') {
			$this->db->insert('sections', $section);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		} else {
			$this->db->where('id', $isUpdate);
			$this->db->update('sections', $section);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
		}

		redirect(base_url('department/'.$departmentID.'/section'));
	}

	/**
	 * Get detail section
	 * @param int $id
	 * @return void
	 */
	public function detail(int $id) : void
	{
		$data = $this->db->where('id', $id)->get('sections',1)->row();
		$reponse = [
			'id'   => $data->id,
			'name' => $data->name
		];
		echo json_encode($data);
	}
}

/* End of file Section.php */
/* Location: ./application/modules/manage/controllers/Section.php */