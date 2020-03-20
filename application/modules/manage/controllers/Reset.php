

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reset extends CI_Controller {

	//private $group, $level, $grade, $section;

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

		$this->load->model('manage_model','manage');
    }
    
    public function index(){

		$data['sections'] = $this->manage->get_all_sections();
        $data['page']           = 'assessment_form_reset';
		$this->load->view('template/template', $data);
	}
	
	public function list_job(){
		$id_section = $this->input->post('id_section');
		
		$jobs = $this->manage->get_jobtitles_list($id_section);
		
		$lists = "<option value=''>Pilih</option>";
		
		foreach($jobs as $data){
			$lists .= "<option value='".$data->id."'>".$data->name."</option>";
		}
		
		$callback = array('list_job'=>$lists); // Masukan variabel lists tadi ke dalam array $callback dengan index array : list_kota
		echo json_encode($callback); // konversi varibael $callback menjadi JSON
	}

	public function submit(){
		$job_id = $this->input->post('job');

		$year_active = $this->db->query("SELECT * FROM assessment_years WHERE is_active = 1")->row();

		$code = "AF-".$job_id."-".$year_active->year;
		
		// reset assessement data by job title
		$this->db->query("DELETE FROM assessment_forms where code = '".$code."'");
		$this->db->query("DELETE FROM assessment_form_questions WHERE form_id = (SELECT id FROM assessment_forms WHERE code = '".$code."' LIMIT 1 )");
		$this->db->query("DELETE FROM assessment_form_state WHERE code_form = '".$code."'");

		$this->session->set_flashdata('success_remove_data', 'Success Remove Assessment Data ');

		$this->index();
	}
}