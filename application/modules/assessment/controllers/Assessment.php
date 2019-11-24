<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment extends CI_Controller {

	private $group, $level, $grade, $section;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login_session')) {
			redirect(base_url('logout'));
		}

		$loginSession = $this->session->userdata('login_session');
		$this->group = $loginSession['group'];
		$this->level = $loginSession['level'];
		$this->grade = $loginSession['grade'];
		$this->section = $loginSession['section'];

		$this->load->model('assessment_model','assessment');
	}

	public function index()
	{
		switch ($this->group) {
			// for admin and HR
			case 1:
			case 2:
				$getJobtitleList = $this->assessment->jobtitle_by_grade(3);
				break;
			// for participant
			default:
				$getJobtitleList = $this->assessment->jobtitle_by_grade_and_section(3, $this->section);
				break;
		}
		$data['jobtitleList'] = $getJobtitleList;
		$data['page'] = 'assessment_v';
		$this->load->view('template/template', $data);
	}

	/**
	 * Show all employes by their jobtitle
	 * @param int $jobtitle
	 * @return void
	 */
	public function form(int $jobtitle) : void
	{
		$data['active_year'] = get_active_year();
		
	}

}

/* End of file Assessment.php */
/* Location: ./application/modules/assessment/controllers/Assessment.php */