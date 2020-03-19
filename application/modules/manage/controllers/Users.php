<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

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
		$data['groups'] = $this->db->get('groups')->result();
		$data['users']  = $this->manage->get_users();
		$data['page']   = 'users_v';
		$this->load->view('template/template', $data);
	}

	/**
	 * Prepare the stored data of new user
	 * 
	 * @return void
	 */
	public function store_preparation() : void
	{
		$nik      = $this->input->post('nik');
		$ldap     = $this->input->post('ldap');
		$group    = $this->input->post('group');
		$level    = $this->input->post('level');
		$email    = $this->input->post('email');
		$isUpdate = $this->input->post('isUpdate');

		$storedData = [
			'nik'      => explode(' - ', $nik)[0],
			'ldap_uid' => $ldap,
			'password' => password_hash('123456', PASSWORD_DEFAULT),
			'group_id' => $group,
			'level'    => $level,
			'email'    => $email
		];

		if (empty($isUpdate)) {
			$this->_store($storedData);
		} else {
			$this->_update($storedData, $isUpdate);
		}		
	}

	/**
	 * Store new user
	 * @param array $user
	 * @return void
	 */
	private function _store($user)
	{
		// check whether NIK was exist
		$this->_is_nik_exist($user['nik']);

		// check whether user was exist
		$this->_is_user_exist($user['nik']);

		$this->db->insert('users', $user);
		$this->session->set_flashdata('success_save_data', 'Successfully saved!');
		redirect(base_url('users'));
	}

	/**
	 * Is user exist ?
	 * @param string $nik
	 * @return void
	 */
	private function _is_user_exist(string $nik) : void
	{
		$check = $this->db->where('nik', $nik)->get('users')->num_rows();
		if ($check > 0) {
			$this->session->set_flashdata('fail_save_data', 'Data not saved! User has exist!');
			redirect(base_url('users'));
		}
		return;
	}
	/**
	 * Get detail user
	 * @param string $nik
	 * @return string
	 */
	public function detail(string $nik)
	{
		$user = $this->manage->get_detail_users($nik);

		$userData = [
			'name'  => $user->name,
			'nik'   => $user->nik,
			'ldap'  => $user->ldap_uid,
			'group' => $user->group_id,
			'level' => $user->level,
			'email' => $user->email
		];

		echo json_encode($userData);
	}

	/**
	 * Update user data
	 * @param array $user
	 * @return void
	 */
	private function _update(array $user)
	{
		$this->db->update('users', $user, ['nik' => $user['nik']]);
		$this->session->set_flashdata('success_update_data', 'Update successfully!');
		redirect(base_url('users'));
	}

	/**
	 * Remove user
	 * @param string $nik
	 * @return void
	 */
	public function remove(string $nik) : void
	{
		$this->db->delete('users', ['nik' => $nik]);
		$this->session->set_flashdata('success_remove_data', 'Remove successfully!');
		redirect(base_url('users'));
	}

	/**
	 * Get employes for autocomplete
	 * 
	 * @return string
	 */
	public function get_employe()
	{
		$this->db->distinct();
		$this->db->select("a.id, a.nik, a.name");
		$this->db->from('employes a');
		$this->db->join('positions b', 'a.position_id = b.id');
		$this->db->where('b.grade >', 3);
		$this->db->like('a.name', $_GET['term'], 'both');
		$this->db->or_like('a.nik', $_GET['term'], 'both');
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
	 * Check whether NIK is exist
	 * @param string $nik
	 * @return void
	 */
	private function _is_nik_exist(string $nik) : void
	{
		$check = $this->db->where('nik', $nik)->get('employes')->num_rows();
		if ($check < 1) {
			$this->session->set_flashdata('fail_save_data', 'Data not saved! NIK was not exist!');
			redirect(base_url('users'));
		}
		return;
	}

}

/* End of file Users.php */
/* Location: ./application/modules/manage/controllers/Users.php */