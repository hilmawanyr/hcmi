<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employes extends CI_Controller {

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
		$data['sections']  = $this->db->where('deleted_at')->get('sections')->result();
		$data['positions'] = $this->db->where('deleted_at')->get('positions')->result();
		$data['employes']  = $this->manage->get_employes()->result();
		$data['page']      = "employes_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Store or update an employe data
	 * 
	 * @return void
	 */
	public function store_preparation() : void
	{
		$nik       = $this->input->post('nik');
		$name      = $this->input->post('name');
		$section   = $this->input->post('section');
		$position  = $this->input->post('position');
		$jobtitle  = $this->input->post('jobtitle');
		$grade     = $this->input->post('grade');
		$isUpdate  = $this->input->post('isUpdate');
		$hiddenNik = $this->input->post('hidden_nik');

		$storedData = [
			'nik'          => $nik,
			'name'         => $name,
			'section_id'   => $section,
			'position_id'  => $position,
			'job_title_id' => $jobtitle,
			'grade'        => $grade
		];

		if (empty($isUpdate)) {
			$this->_store($storedData);
		} else {
			$this->_update($storedData, $hiddenNik,$isUpdate);
		}
	}

	/**
	 * Store new data
	 * @param array $data
	 * @return void
	 */
	private function _store(array $data) : void
	{
		// check whether NIK was exist
		$this->_is_nik_exist($data['nik']);

		$this->db->insert('employes', $data);
		$this->session->set_flashdata('success_save_data', 'Successfully saved!');
		redirect(base_url('employes'));
	}

	/**
	 * Update data
	 * @param array $data
	 * @param string $hiddenNik
	 * @param int $id
	 * @return void
	 */
	private function _update(array $data, string $hiddenNik, int $id) : void
	{
		// check whether NIK was exist
		if ($data['nik'] != $hiddenNik) {
			$this->_is_nik_exist($data['nik']);
		}

		$this->db->where('id', $id)->update('employes',$data);
		$this->session->set_flashdata('success_update_data', 'Update successfully!');
		redirect(base_url('employes'));
	}

	/**
	 * Get job title base on section and position
	 * @param int $section
	 * @param int $position
	 * @return void
	 */
	public function get_jobtitle(int $section, int $position) : void
	{
		$jobTitle = $this->db->where('section', $section)->where('position_id', $position)->get('job_titles')->result();

		$list = "<option value='' selected='' disabled=''></option>";
		foreach($jobTitle as $row) {
			$list .= "<option value='".$row->id."'>";
			$list .= $row->name;
			$list .= "</option>";
		}
		echo $list;
	}
	

	/**
	 * Check whether NIK is exist
	 * @param string $nik
	 * @return void
	 */
	private function _is_nik_exist(string $nik) : void
	{
		$check = $this->db->where('nik', $nik)->get('employes')->num_rows();
		if ($check > 0) {
			$this->session->set_flashdata('fail_save_data', 'Data not saved! NIK was exist!');
			redirect(base_url('employes'));
		}
		return;
	}

	/**
	 * Get employe detail
	 * @param string $nik
	 * @return void
	 */
	public function detail(string $nik) : void
	{
		$employe = $this->db->where('nik', $nik)->get('employes')->row();
		$data = [
			'id'       => $employe->id,
			'nik'      => $employe->nik,
			'name'     => $employe->name,
			'section'  => $employe->section_id,
			'position' => $employe->position_id,
			'jobtitle' => $employe->job_title_id,
			'grade'    => $employe->grade
		];
		echo json_encode($data);
	}

	/**
	 * Nonactivated employe
	 * @param string $nik
	 * @return void
	 */
	public function set_employe_status(string $nik) : void
	{
		$check = $this->db->where('nik', $nik)->get('employes')->row();
		if (is_null($check->deleted_at)) {
			$this->db->where('nik', $nik)->update('employes',['deleted_at' => date('Y-m-d H:i:s')]);
			$this->session->set_flashdata('success_update_data', 'Disactivated successfully!');
		} else {
			$this->db->where('nik', $nik)->update('employes',['deleted_at' => NULL]);
			$this->session->set_flashdata('success_update_data', 'Activated successfully!');
		}
		redirect(base_url('employes'));
	}
	

}

/* End of file Employes.php */
/* Location: ./application/modules/manage/controllers/Employes.php */