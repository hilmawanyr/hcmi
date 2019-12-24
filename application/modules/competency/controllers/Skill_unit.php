<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Skill_unit extends CI_Controller {

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

		$this->load->model('dictionary_competency','dictionary');
	}

	/**
	 * Get skill unit by id dictionary
	 * @param int $dictionaryId
	 * @return void
	 */
	public function get_skill_unit(int $dictionaryId) : void
	{
		$data['skillTypes'] = $this->dictionary->get_skill_types();
		$data['dictionary'] = $this->dictionary->get_dictionary_detail($dictionaryId);
		$data['skillUnit']  = $this->dictionary->skill_unit_by_dictionary($dictionaryId);
		$data['page']       = "skill_unit_v";
		$this->load->view('template/template', $data);
	}
	
	/**
	 * Print list of skill unit base on dictionary id
	 * @param int $dictionaryId
	 * @return void
	 */
	public function print_skill_unit(int $dictionaryId) : void
	{
		$data['dictionary'] = $dictionaryId;
		$data['skillUnit']  = $this->dictionary->skill_unit_by_dictionary($dictionaryId);
		$this->load->view('skill_unit_print', $data);
	}

	/**
	 * Store new skill unit data to db
	 * 
	 * @return void
	 */
	public function store() : void
	{
		$isUpdate     = $this->input->post('isUpdate');
		$dictionaryId = $this->input->post('dictionary_id');
		$levelDes     = $this->input->post('level_des');
		$level        = $this->input->post('level');
		$description  = $this->input->post('description');

		$storedData = [
			'id_dictionary' => $dictionaryId,
			'level_desc'    => $levelDes,
			'level'         => $level,
			'description'   => $description
		];

		if ($isUpdate == '') {
			$this->db->insert('skill_units', $storedData);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		} else {
			$this->db->where('id', $isUpdate);
			$this->db->update('skill_units', $storedData);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
		}
		redirect(base_url('skill_unit/'.$dictionaryId.'/dictionary'));
	}

	/**
	 * Get skill unit detail
	 * @param int $skillUnitId
	 * @return void
	 */
	public function detail(int $skillUnitId) : void
	{
		$skillUnits = $this->dictionary->get_skill_unit($skillUnitId);
		
		foreach ($skillUnits as $skill) {
			$skillUnit = [
				'level_des'   => $skill->level_desc,
				'level'       => $skill->level,
				'description' => $skill->description
			];
		}

		echo json_encode($skillUnit);
	}

	/**
	 * Remove skill unit data
	 * @param int $skillUnitId
	 * @param int $dictionaryId
	 * @return void
	 */
	public function remove(int $skillUnitId, int $dictionaryId) : void
	{
		$this->db->where('id', $skillUnitId);
		$this->db->update('skill_units', ['deleted_at' => date('Y-m-d H:i:s')]);
		$this->session->set_flashdata('success_remove_data', 'Data successfully removed!');
		redirect(base_url('skill_unit/'.$dictionaryId.'/dictionary'));
	}
}

/* End of file Skill_unit.php */
/* Location: ./application/modules/competency/controllers/Skill_unit.php */