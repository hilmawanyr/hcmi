<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Information_board extends CI_Controller {

	private $group, $level, $grade, $section, $userid;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login_session')) {
			redirect(base_url('logout'));
		}

		$loginSession  = $this->session->userdata('login_session');
		$this->userid  = $loginSession['nik'];
		$this->group   = $loginSession['group'];
		$this->level   = $loginSession['level'];
		$this->grade   = $loginSession['grade'];
		$this->section = $loginSession['section'];

		$this->load->model('manage_model','manage');
	}

	public function index()
	{
		$data['informations'] = $this->_get_information()->result();
		$data['page']         = "information_board_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Get all or specific information. Dispatch param if you want to get specific information.
	 * @param int $id; default null
	 * @return object
	 */
	private function _get_information(int $id = null) : object
	{
		$information = $this->manage->get_information($id);
		return $information;
	}

	/**
	 * Show create information page
	 * 
	 * @return void
	 */
	public function create() : void
	{
		$isUpdate          = 0;
		$data['positions'] = $this->db->like('name', 'manager', 'BOTH')->get('positions')->result();
		$data['page']      = "create_information_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Create new data information to stored to database
	 * 
	 * @return void
	 */
	public function create_store() : void
	{
		$title    = $this->input->post('title');
		$content  = $this->input->post('content');
		$type     = $this->input->post('type');
		$position = $this->input->post('position');
		$isUpdate = (int)$this->input->post('is_update');

		$storedData['title']      = $title;
		$storedData['content']    = $content;
		$storedData['type']       = $type;
		$storedData['position']   = $position;

		if ($isUpdate != '') {
			$storedData['updated_at'] = date('Y-m-d H:i:s');
			$storedData['updated_by'] = $this->userid;
		} else {
			$storedData['created_at'] = date('Y-m-d H:i:s');
			$storedData['created_by'] = $this->userid;
		}
		
		$this->_store_data($isUpdate, $storedData);
	}

	/**
	 * Store 'information' data to database; insert or update
	 * Update will be execute if $isUpdate does not null
	 * @param int $isUpdate
	 * @param array $data
	 * @return void
	 */
	private function _store_data(int $isUpdate=null, array $data) : void
	{
		switch ($isUpdate) {
			case null:
				$this->db->insert('informations', $data);
				$this->session->set_flashdata('success_save_data', 'Saved successfully!');
				redirect(base_url('information'));
				break;
			
			default:
				$this->db->where('id', $isUpdate)->update('informations',$data);
				$this->session->set_flashdata('success_update_data', 'Update successfully!');
				redirect(base_url('information'));
				break;
		}
	}

	/**
	 * See detail information
	 * @param int $id
	 * @return void
	 */
	public function detail(int $id) : void
	{
		$data['information'] = $this->_get_information($id)->row();
		$data['page']        = "detail_information_v";
		$this->load->view('template/template', $data);		
	}

	/**
	 * Edit nformation
	 * @param int $id
	 * @return void
	 */
	public function edit(int $id) : void
	{
		$data['isUpdate']    = $id;
		$data['positions']   = $this->db->like('name', 'manager', 'BOTH')->get('positions')->result();
		$data['information'] = $this->_get_information($id)->row();
		$data['page']        = 'create_information_v';
		$this->load->view('template/template', $data);		
	}

	/**
	 * Remove data. Just update deleted_at column
	 * @param int $id
	 * @return void
	 */
	public function delete(int $id) : void
	{
		$updatedData = ['deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $this->userid];
		$this->db->where('id', $id)->update('informations', $updatedData);
		$this->session->set_flashdata('success_remove_data', 'Data successfully removed!');
		redirect(base_url('information'));		
	}

}

/* End of file Information_board.php */
/* Location: ./application/modules/manage/controllers/Information_board.php */