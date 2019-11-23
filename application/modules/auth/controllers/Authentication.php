<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

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
		$isUserExist 	= $this->db->where('nik', $nik)->get('users')->row();
		
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
	private function _login_success(stdClass $userData): void
	{
		$dataLogin = [
			'nik' => $userData->nik,
			'group' => $userData->group,
			'level' => $userData->level
		];
		
		$this->session->set_userdata('login_session',$dataLogin);
		redirect('dashboard');
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
	
}

/* End of file Authentication.php */
/* Location: ./application/modules/auth/controllers/Authentication.php */