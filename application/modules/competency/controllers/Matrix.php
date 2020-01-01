<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Matrix extends CI_Controller {

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

		$this->load->model('matrix_model','matrix');
	}

	public function index()
	{
		$data['sections']  = $this->db->where('deleted_at')->get('sections')->result();
		$data['positions'] = $this->db->where('deleted_at')->get('positions')->result();
		$data['jobtitles'] = $this->matrix->get_jobtitle()->result();
		$data['page']      = "matrix_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Add new job title
	 * 
	 * @return void
	 */
	public function store() : void
	{
		$position = $this->input->post('position');
		$section  = $this->input->post('section');
		$group    = $this->input->post('group');
		$jobtitle = $this->input->post('jobtitle');

		$this->_is_jobtitle_exist($section, $position, $jobtitle);

		$storedObject = [
			'section'     => $section,
			'position_id' => $position,
			'group'       => $group,
			'name'        => $jobtitle
		];
		$this->db->insert('job_titles', $storedObject);
		$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		redirect(base_url('competency_matrix'));
	}

	/**
	 * Check whether job title with same position and section is exist.
	 * @param string $section
	 * @param string $position
	 * @param string $jobtitle
	 * @return void
	 */
	private function _is_jobtitle_exist(string $section, string $position, string $jobtitle) : void
	{
		$isJobtitleExist = $this->db->where('position_id', $position)
									->where('section', $section)
									->where('name', $jobtitle)
									->get('job_titles')
									->num_rows();
		if ($isJobtitleExist > 0) {
			$this->session->set_flashdata('fail_save_data', 'Data not saved. Same data has exist!');
			redirect(base_url('competency_matrix'));
		}
		return;
	}

	/**
	 * Show competency matrix in the job title and manage it
	 * @param int $jobtitle
	 * @return void
	 */
	public function manage(int $jobtitle) : void
	{
		$data['jobtitle_id']  = $jobtitle;
		$data['competencies'] = $this->matrix->get_competency($jobtitle);
		$data['jobtitle']     = $this->matrix->get_jobtitle($jobtitle);
		$data['matrixes']     = $this->matrix->get_competency_matrix($jobtitle);
		$data['page']         = "manage_matrix_v";
		$this->load->view('template/template', $data);		
	}

	/**
	 * Store competency matrix
	 * 
	 * @return void
	 */
	public function store_competency() : void
	{
		$level      = $this->input->post('level');
		$competency = $this->input->post('competency');
		$jobtitle   = $this->input->post('jobtitle');

		$this->_is_competency_exist($level, $competency, $jobtitle);

		$storedObject = [
			'level'      => $level,
			'skill_id'   => $competency,
			'job_id'     => $jobtitle,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		];
		$this->db->insert('skill_matrix', $storedObject);
		$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		redirect(base_url('competency_matrix/'.$jobtitle.'/manage'));
	}

	/**
	 * Check whether competency has exist for this job title.
	 * @param int $level
	 * @param int $competency
	 * @param int $jobtitle
	 * @return void
	 */
	private function _is_competency_exist(int $level, int $competency, int $jobtitle) : void
	{
		$isCompetencyExist = $this->db->where('level', $level)
									->where('skill_id', $competency)
									->where('job_id', $jobtitle)
									->where('deleted_at')
									->get('skill_matrix')
									->num_rows();
		if ($isCompetencyExist > 0) {
			$this->session->set_flashdata('fail_save_data', 'Data not saved. Same data has exist!');
			redirect(base_url('competency_matrix/'.$jobtitle.'/manage'));
		}
		return;
	}

	/**
	 * Remove competency from skill matrix
	 * @param int $jobtitle
	 * @param int $id
	 * @return void
	 */
	public function remove_competency(int $id, int $jobtitle) : void
	{
		$this->db->where('id', $id)->update('skill_matrix',['deleted_at' => date('Y-m-d H:i:s')]);
		$this->session->set_flashdata('success_remove_data', 'Data successfully removed!');
		redirect(base_url('competency_matrix/'.$jobtitle.'/manage'));
	}

}

/* End of file Matrix.php */
/* Location: ./application/modules/competency/controllers/Matrix.php */