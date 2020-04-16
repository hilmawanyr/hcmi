<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('login');
	}

	public function index()
	{
		if ($this->session->userdata('login_session')) {
			redirect('dashboard','refresh');
		}

		$data['informations'] = $this->db->where('deleted_at')->where('type', 'PUBLIC')->get('informations')->result();
		$this->load->view('authentication_v', $data);
	}

	/**
	 * Handle user login attempt
	 * 
	 * @return void
	 */
	public function attempt_login()
	{
		 $ldap_id 			= $this->input->post('nik');
		 $password 			= $this->input->post('password');

		 if($ldap_id == 123456){
			$nik 			= $ldap_id;
			
			$isUserExist 	= $this->login->is_user_exist($nik);
			
			if (count($isUserExist) > 0) {
				// do verification for user's password
				if (password_verify($password, $isUserExist->password)) {
					// create log for success login
					$this->_auth_log($nik);
					// create login session and redirect them to dahsboard
					$this->_login_success($isUserExist);
				}
				// handle fail login - wrong password
				$this->session->set_flashdata('login_fail', 'Your password is wrong!');
				redirect('auth/authentication');
			}
			// handle fail login - wrong password
			$this->session->set_flashdata('login_fail', 'Account not found!');
			 
		 }else{
		 	$user = $this->db->where('ldap_uid', $ldap_id)->get("users")->row();
		 	$nik = $user->nik;
		 	
		 	if ($nik != "") {
		 		// do verification for user's password
		 		if ($this->ldap_verification($ldap_id, $password)) {
		 			// create log for success login
		 			$this->_auth_log($nik);
		 			// create login session and redirect them to dahsboard
		 			$this->_login_success($user);
		 		}
		 		// handle fail login - wrong password
		 		$this->session->set_flashdata('login_fail', 'Your password is wrong!');
		 		redirect('auth/authentication');
		 	}
		 	// handle fail login - wrong password
		 	$this->session->set_flashdata('login_fail', 'Account not found!');
		}
	     redirect('auth/authentication');
	}

	public function ldap_verification($ldap_id, $password)
	{
		$ldap = "ldap://10.87.42.4";
		$ldap_port = 389;

		// $ldap_dn = "uid=71470456,dc=kenki.global.hitachi,dc=net";
		// $ldap_password = "alexis66";

		$ldap_dn = "uid=".$ldap_id.",dc=kenki.global.hitachi,dc=net";
		$ldap_password = $password;

		// echo "# Connecting to".$ldap."\n";
		$ldap_con = ldap_connect($ldap, $ldap_port) or die("cannot connect");
		// echo "Connection success\n";

		if (!ldap_set_option($ldap_con, LDAP_OPT_PROTOCOL_VERSION, 3)) {
			echo 'Cannot set protocol v3';
		}

		if (!ldap_set_option($ldap_con, LDAP_OPT_REFERRALS, 0)) {
			echo "Cannot set referral 0";
		}

		$bind = ldap_bind($ldap_con,$ldap_id."@kenki.global.hitachi.net", $ldap_password);

		ldap_get_option($ldap_con, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);

		// check error lda connection
		// echo $err;
		// var_dump($bind);

		if ($bind) {
			if (ldap_get_option($ldap_con, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err)) {
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Handle success login
	 * @param object $userData
	 * @return void
	 */
	private function _login_success(stdClass $userData) : void
	{
		$createDataLogin = $this->_prepare_user_data($userData->nik);
		$this->session->set_userdata('login_session',$createDataLogin);
		redirect('dashboard');
	}

	/**
	 * Store auth log to db, for success login
	 * @param int $nik
	 * @return void
	 */
	private function _auth_log(string $nik) : void
	{
		$time = date('Y-m-d H:i:s');
		// update last_login in users table
		$this->db->where('nik', $nik)->update('users', ['last_login' => $time]);
		// store log to log_auth table
		$this->db->insert('log_auth', ['nik' => $nik, 'last_login' => $time]);
		return;
	}

	/**
	 * Prepare user's data for session login usage
	 * @param string $nik
	 * @return array
	 */
	private function _prepare_user_data(string $nik) : array
	{
		$getUserData = $this->login->get_user($nik);

		$dataLogin = [
			'nik'            => $getUserData->nik,
			'name'           => $getUserData->name,
			'section'        => $getUserData->section_id,
			'position'       => $getUserData->position_id,
			'position_grade' => $this->login->get_position_grade($getUserData->position_id),
			'job_title'      => $getUserData->job_title_id,
			'grade'          => $getUserData->grade,
			'group'          => $getUserData->group_id,
			'level'          => $getUserData->level,
			'department'     => $getUserData->id
		];

		// nurfan log var_dump($dataLogin);die();
		
		return $dataLogin;
	}

	/**
	 * Handle user logout. Clear login session.
	 * 
	 * @return void
	 */
	public function logout()
	{
		$this->session->sess_destroy();
		redirect('auth/authentication','refresh');
	}

	/**
	 * Handle user change password.
	 * 
	 * @return void
	 */
	public function edit_pass()
	{
		$this->_auth_verification();

		$data['page'] = "change_password_v";
		$this->load->view('template/template', $data);
	}

	public function update_pass()
	{	
		$this->_auth_verification();

		$nik 			= $this->input->post('nik');
		$password 		= $this->input->post('current_pass');
		$new_pass 		= $this->input->post('new_pass');
		$repeat_pass 	= $this->input->post('repeat_pass');
		$isUserExist 	= $this->login->is_user_exist($nik);

		// validation for  new pass and repeate pass
		if ($new_pass != $repeat_pass) {
			$this->session->set_flashdata('fail_save_data', 'New pasword do not match with repeated password!');
			redirect(base_url('changepassword'));
		}
		
		if ($isUserExist) {
			// do verification for user's password
			if (password_verify($password, $isUserExist->password)) {
				$hash_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

				$this->login->update_password($nik, $hash_new_pass);
				$this->session->set_flashdata('success_update_data', 'Password updated successfully! Please logout and relogin to try your new password!');
				redirect('changepassword');
			}

			$this->session->set_flashdata('fail_save_data', "Failed, old password not match");
			redirect('changepassword');
		}

		$this->session->set_flashdata('fail_save_data', "Error user session, please contact administrator");
		redirect('changepassword');
	}

	/**
	 * See detail of authentication login
	 * 
	 * @return void
	 */
	public function auth_log() : void
	{
		$this->_auth_verification(1);

		$data['logs'] = $this->db->get('users')->result();
		$data['page'] = 'auth_log';
		$this->load->view('template/template', $data);		
	}

	/**
	 * Print auth log
	 * @param int $nik
	 * @return void
	 */
	public function print_log(string $nik = NULL) : void
	{
		$this->_auth_verification();
		// set file name if print is for specific users
		$data['nik']  = $nik;
		$data['logs'] = $this->login->get_auth_log($nik);
		$this->load->view('log_auth_excel', $data);
	}
	
	/**
	 * Auth verification fo some method
	 * @param int $checkForAuthLogMenu; default NULL
	 * @return void
	 */
	private function _auth_verification(int $checkForAuthLogMenu = NULL) : void
	{
		$login_sess = $this->session->userdata('login_session');
		if (!$login_sess) {
			$this->logout();
		}
		if (!is_null($checkForAuthLogMenu)) {
			// HR: level = 1, group = 2
			// Admin: level 1, group = 1
			if (($login_sess['level'] == 1 && $login_sess['group'] == 2) || ($login_sess['level'] == 1 && $login_sess['group'] == 1)) {
				redirect('dashboard');
			}	
		}
		return;
	}
}

/* End of file Authentication.php */
/* Location: ./application/modules/auth/controllers/Authentication.php */
