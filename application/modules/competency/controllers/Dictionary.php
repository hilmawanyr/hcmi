<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dictionary extends CI_Controller {

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

	public function index()
	{
		$data['skillTypes'] = $this->dictionary->get_skill_types();
		$data['page']       = "dictionary_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Get list of dictionary by skill type id
	 * @param int $skillTypeId
	 * @return void
	 */
	public function get_dictionary(int $skillTypeId) : void
	{
		$data['skillTypes']   = $this->dictionary->get_skill_types();
		$data['skillTypeId']  = $skillTypeId;
		$data['dictionaries'] = $this->dictionary->get_dictionary_competency($skillTypeId);
		$this->load->view('dictionary_list_v', $data);
	}

	/**
	 * Store new competency dictionary to db
	 * 
	 * @return void
	 */
	public function store_competency() : void
	{
		$isUpdate    = $this->input->post('isUpdate');
		$skillId     = $this->input->post('skill_type');
		$nameID      = $this->input->post('name_id');
		$nameEN      = $this->input->post('name_en');
		$description = $this->input->post('description');

		$storedData = [
			'name_id'     => $nameID,
			'name_en'     => $nameEN,
			'description' => $description,
			'skill_group' => $skillId
		];

		// make condition, is action is create or update
		if ($isUpdate == "") {
			$this->db->insert('skill_dictionaries', $storedData);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		} else {
			$this->db->where('id', $isUpdate);
			$this->db->update('skill_dictionaries', $storedData);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
		}
		
		redirect(base_url('dictionary'));
	}

	/**
	 * Edit competency, load detail of competency dictionary
	 * @param int $dictionaryId
	 * @return void
	 */
	public function edit_competency(int $dictionaryId) : void
	{
		$getDictionary = $this->dictionary->get_dictionary_detail($dictionaryId);
		$dictionary = [
			'id'          => $getDictionary->id,
			'name_id'     => $getDictionary->name_id,
			'name_en'     => $getDictionary->name_en,
			'description' => $getDictionary->description,
			'skill_type'  => $getDictionary->skill_group
		];
		echo json_encode($dictionary);
	}

	/**
	 * Remove competency from db
	 * @param int $dictionaryId
	 * @return void
	 */
	public function remove_competency(int $dictionaryId) : void
	{
		$this->db->where('id', $dictionaryId);
		$this->db->update('skill_dictionaries',['deleted_at' => date('Y-m-d H:i:s')]);
		$this->session->set_flashdata('success_remove_data', 'Data successfully removed!');
		redirect(base_url('dictionary'));
	}

	/**
	 * Export dictionary list to excel file
	 * @param int $skillTypeId
	 * @return void
	 */
	public function print_dictionary(int $skillTypeId) : void
	{
		$data['skillTypeId']  = $skillTypeId;
		$data['dictionaries'] = $this->dictionary->get_dictionary_competency($skillTypeId);
		$this->load->view('dictionary_print', $data);
	}
	
	/**
	 * Get skill unit by id dictionary
	 * @param int $dictionaryId
	 * @return void
	 */
	public function get_skill_unit(int $dictionaryId) : void
	{
		$data['dictionary'] = $this->dictionary->get_dictionary_detail($dictionaryId);
		$data['skillUnit']  = $this->dictionary->get_skill_unit($dictionaryId);
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
		$data['skillUnit']  = $this->dictionary->get_skill_unit($dictionaryId);
		$this->load->view('skill_unit_print', $data);
	}
}

/* End of file Dictionary.php */
/* Location: ./application/modules/competency/controllers/Dictionary.php */