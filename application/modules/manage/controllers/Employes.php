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
	 * Get employes by autocomplete
	 * 
	 * @return string
	 */
	public function get_employe()
	{
		$this->db->distinct();
		$this->db->select("id, nik, name");
		$this->db->from('employes');
		$this->db->like('name', $_GET['term'], 'both');
		$this->db->or_like('nik', $_GET['term'], 'both');
		$sql  = $this->db->get();
		$data = [];

		foreach ($sql->result() as $row) {
			$data[] = [
				'id_kary' => $row->id,
				'nik'     => $row->nik,
				'value'   => $row->nik . ' - ' . $row->name
			];
		}
		echo json_encode($data);
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
		$head      = explode(' - ', $this->input->post('head'))[0];

		$storedData = [
			'nik'          => $nik,
			'name'         => $name,
			'dept_id'      => get_department_by_section($section)->id,
			'section_id'   => $section,
			'position_id'  => $position,
			'job_title_id' => $jobtitle,
			'grade'        => $grade,
			'head'         => $head
		];

		if (empty($isUpdate)) {
			$this->_store($storedData);
		} else {
			$this->_update($storedData, $hiddenNik, $isUpdate);
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

		$storedData = array_filter($data, function($arr) {
			return $arr != 'head';
		}, ARRAY_FILTER_USE_KEY);

		$this->db->insert('employes', $storedData);

		$get_last_id = $this->db->insert_id();

		$last_employe = $this->db->get_where('employes', ['id' => $get_last_id])->row();

		// store to employe relation table
		$this->_store_employe_relation($data['head'], $last_employe->nik);

		$this->session->set_flashdata('success_save_data', 'Successfully saved!');
		redirect(base_url('employes'));
	}

	/**
	 * Insert to employe_relation table
	 * @param string
	 * @return void
	 */
	private function _store_employe_relation(string $head, string $nik) : void
	{
		// check whether head's NIK was exist
		$this->_is_heads_nik_exist($nik, $head);

		$object = ['nik' => $nik, 'head' => $head, 'created_at' => date('Y-m-d H:i:s')];
		$this->db->insert('employe_relations', $object);

		return;
	}

	/**
	 * Check whether head's nik was exist
	 * @param string $nik
	 * @return void
	 */
	private function _is_heads_nik_exist(string $nik, string $headNik) : void
	{
		$check = $this->db->where('nik', $headNik)->get('employes')->num_rows();
		if ($check < 1) {
			$this->db->delete('employes', ['nik' => $nik]);
			$this->session->set_flashdata('fail_save_data', 'Data not saved! The head\'s NIK was not exist!');
			redirect(base_url('employes'));
		}
		return;
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

		$this->_update_employe_relation($data['nik'], $data['head']);

		$this->session->set_flashdata('success_update_data', 'Update successfully!');
		redirect(base_url('employes'));
	}

	/**
	 * Update employe relation
	 * @param 
	 *
	 */
	private function _update_employe_relation($nik, $head) : void
	{
		$this->db->update('employe_relations', ['nik' => $nik, 'head' => $head], ['nik' => $nik]);
		return;
	}

	/**
	 * Get job title base on section and position
	 * @param int $section
	 * @param int $position
	 * @return void
	 */
	public function get_jobtitle(int $section, int $position) : void
	{
		$jobTitle = $this->db->get_where('job_titles',['section' => $section,'position_id' => $position])->result();
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
		$employe = $this->db->select('a.*, b.head')
							->from('employes a')
							->join('employe_relations b','a.nik = b.nik', 'left')
							->where('a.nik', $nik)
							->get()->row();

		$data = [
			'id'       => $employe->id,
			'nik'      => $employe->nik,
			'name'     => $employe->name,
			'section'  => $employe->section_id,
			'position' => $employe->position_id,
			'jobtitle' => $employe->job_title_id,
			'grade'    => $employe->grade,
			'head'     => $employe->head .' - '. user_name($employe->head)
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