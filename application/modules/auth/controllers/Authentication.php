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

		$this->load->view('authentication_v');
	}

	/**
	 * Handle user login attempt
	 * 
	 * @return void
	 */
	public function attempt_login()
	{
		$nik 			= $this->input->post('nik');
		$password 		= $this->input->post('password');
		$isUserExist 	= $this->login->is_user_exist($nik);
		
		// if (count($isUserExist) > 0) {
		// 	// do verification for user's password
		// 	if (password_verify($password, $isUserExist->password)) {
		// 		// create login session and redirect them to dahsboard
		// 		$this->_login_success($isUserExist);
		// 	}
		// 	// handle fail login - wrong password
		// 	$this->session->set_flashdata('login_fail', 'Your password is wrong!');
		// 	redirect('auth/authentication');
		// }
		// handle fail login - wrong password
		$this->session->set_flashdata('login_fail', 'Account not found!');
		redirect('auth/authentication');
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
	 * Prepare user's data for session login usage
	 * @param string $nik
	 * @return array
	 */
	private function _prepare_user_data(string $nik) : array
	{
		$getUserData = $this->login->get_user($nik);

		$dataLogin = [
			'nik' 		=> $getUserData->nik,
			'name' 		=> $getUserData->name,
			'section' 	=> $getUserData->section_id,
			'position' 	=> $getUserData->position_id,
			'job_title' => $getUserData->job_title_id,
			'grade' 	=> $getUserData->grade,
			'group' 	=> $getUserData->group_id,
			'level' 	=> $getUserData->level
		];
		
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
		$data['page'] = "change_password_v";
		$this->load->view('template/template', $data);
	}

	public function update_pass()
	{	
		$nik 			= $this->input->post('nik');
		$password 		= $this->input->post('old_pass');
		$new_pass 		= $this->input->post('new_pass');
		// $repeat_pass 	= $this->input->post('repeat_pass');
		$isUserExist 	= $this->login->is_user_exist($nik);
		
		if ($isUserExist) {
			// do verification for user's password
			if (password_verify($password, $isUserExist->password)) {
				$hash_new_pass = password_hash($new_pass, 1, null);

				$this->login->update_password($nik, $new_pass);
				$this->session->set_flashdata('result', true);
				$this->session->set_flashdata('alert_class', "alert alert-success alert-dismissible");
				$this->session->set_flashdata('result_message', "Success change password");
				redirect('auth/authentication/edit_pass');
			}
			
			$this->session->set_flashdata('result', false);
			$this->session->set_flashdata('alert_class', "alert alert-danger alert-dismissible");
			$this->session->set_flashdata('result_message', "Failed, old password not match");
			redirect('auth/authentication/edit_pass');
		}

		$this->session->set_flashdata('result', false);
		$this->session->set_flashdata('alert_class', "alert alert-danger alert-dismissible");
		$this->session->set_flashdata('result_message', "Error user session, please contact administrator");
		redirect('auth/authentication/edit_pass');
	}
	
}

/* End of file Authentication.php */
/* Location: ./application/modules/auth/controllers/Authentication.php */