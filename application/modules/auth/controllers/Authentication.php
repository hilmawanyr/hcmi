<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('login_session')) {
			redirect('dashboard','refresh');
		}
	}

	public function index()
	{
		$this->load->view('authentication_v', $data);
	}

}

/* End of file Authentication.php */
/* Location: ./application/modules/auth/controllers/Authentication.php */