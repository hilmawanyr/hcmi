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
		
		if (count($isUserExist) > 0) {
			// do verification for user's password
			if (password_verify($password, $isUserExist->password)) {
				// create login session and redirect them to dahsboard
				$this->_login_success($isUserExist);
			}
			// handle fail login - wrong password
			$this->session->set_flashdata('login_fail', 'Your password is wrong!');
			redirect('auth/authentication');
		}
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
	
}

/* End of file Authentication.php */
/* Location: ./application/modules/auth/controllers/Authentication.php */