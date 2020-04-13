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
		$head      = $this->input->post('head');
		$dept 	   = NULL;
		
		if ($head != "") {
			$head      = explode(' - ', $this->input->post('head'))[0];	
		}
		
		if ($section != ""){
			$dept     = get_department_by_section($section)->id;
		}

		$storedData = [
			'nik'          => $nik,
			'name'         => $name,
			'dept_id'      => $dept,
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
		$this->_is_nik_exist($data['nik']);

		$storedData = array_filter($data, function($arr) {
			return $arr != 'head';
		}, ARRAY_FILTER_USE_KEY);

		$this->db->insert('employes', $storedData);

		if ($data['nik'] != "") {
			// store to employe relation table
			$this->_store_employe_relation($data['head'], $data['nik']);	
		}

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
		$this->db->insert('employee_relations', $object);

		return;
	}

	/**
	 * Check whether head's nik was exist
	 * @param string $nik
	 * @return void
	 */
	private function _is_heads_nik_exist(string $nik, string $head) : void
	{
		$check = $this->db->where('nik', $head)->get('employes')->num_rows();
		if ($check < 1) {
			$this->session->set_flashdata('warning', 'Employee data has been saved. But the employee\'s supervisor data is not saved/updated because his nik is not available!');
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
		$update_data = array_filter($data, function($arr) {
			return $arr != 'head';
		}, ARRAY_FILTER_USE_KEY);

		$this->db->where('nik', $data['nik'])->update('employes',$update_data);
		
		// check whether the employee has a head before
		if ($this->_is_relation_exist($data['nik'])) {
			$this->_update_employe_relation($data['nik'], $data['head']);
		} else {
			$this->_store_employe_relation($data['head'], $data['nik']);
		}

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
		$this->db->where('nik' , $nik);
		$this->db->update('employee_relations', ['head' => $head]);
		return;
	}

	/**
	 * Get job title base on section and position
	 * @param int $section
	 * @param int $position
	 * @return void
	 */
	public function get_jobtitle($section=0, $position=0, $jobtitle="") : void
	{
		if ($section != 0 && $position != 0) {
			$jobTitle = $this->db->get_where('job_titles',['section' => $section,'position_id' => $position])->result();
			$list = "<option value='' disabled=''></option>";
			foreach($jobTitle as $row) {
				$list .= $jobtitle == $row->id ? "<option value='".$row->id."' selected=''>" : "<option value='".$row->id."'>";
				$list .= $row->name;
				$list .= "</option>";
			}
			echo $list;
		}
		return;
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

	private function _is_relation_exist(string $nik) : bool
	{
		$check = $this->db->where('nik', $nik)->get('employee_relations')->num_rows();
		if ($check > 0) {
			return TRUE;
		}

		return FALSE;
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
							->join('employee_relations b','a.nik = b.nik', 'left')
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
	
	/**
	 * Get grade depend on position
	 * @param int $position_id
	 * @return void
	 */
	public function get_grade(int $position_id) : void
	{
		$grade = $this->db->get_where('positions', ['id' => $position_id])->row()->grade;
		echo $grade;		
	}

}

/* End of file Employes.php */
/* Location: ./application/modules/manage/controllers/Employes.php */
