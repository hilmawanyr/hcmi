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

		$get_employes = $this->db->where('job_title_id', $jobtitle)->get('employes');
		$data['jobTitleName'] = $this->db->where('id', $jobtitle)->get('job_titles');

		// load competency
		$data['dictionary'] = $this->assessment->get_competency($jobtitle);

		// check whether assessment formhas generate or not
		$isFormExist = $this->assessment->is_assessment_form_exist($data['active_year'], $jobtitle);

		// generate form if job title has competency
		if ($data['dictionary']->num_rows() > 0) {
			// insert form assessment if those doesn't exist
			if ($isFormExist->num_rows() < 1) {
				$this->_generate_form_assessment($get_employes, $jobtitle);
			}

			// change value of $get_employe if job_titles has competency matrixes
			$get_employes = $this->assessment->competency_by_jobtitle($data['active_year'], $jobtitle);
		}

		// check whether form has submited or not
		$data['isSubmited'] = $this->db->like('code', 'AF-'.$jobtitle.'-'.$data['active_year'], 'BOTH')
										->get('assessment_validations')
										->num_rows();

		/** then check number of assessment per job title
         * and compare with number of complete assessment
         * to get the comparison which will be use
         * to check whether the form can be submit or not
         */
		// number of statement per job title
		$data['statementAmount'] = $isFormExist->num_rows() * $data['dictionary']->num_rows();

		// number of filled assessment
		$data['completeAssessment'] = $this->assessment->complete_assessment($jobtitle);
		$data['employes'] = $get_employes;
		$data['page'] = 'assessment_form_v';
		$this->load->view('template/template', $data);
	}

	/**
	 * Create form assessment content
	 * @param array $employes
	 * @param int $jobtitle
	 * @return void
	 */
	private function _generate_form_assessment(array $employes, int $jobtitle) : void
	{
		$activeYear = get_active_year();

		foreach ($employes as $employe) {
			$isEmployeHasForm = $this->assessment->is_employe_has_form(
									$employe->job_title_id,
									$activeYear,
									$employe->nik
								);
			if ($isEmployeHasForm < 1) {
				// create an array of assessment form data to make insert batch
				$assessmentForm[] = [
					'code' => 'AF-'.$employe->job_title_id.'-'.$activeYear,
					'nik' => $employe->nik,
					'job_id' => $employe->job_title_id
				];
			}
		}
		$this->db->insert_batch('assessment_forms', $assessmentForm);

		// insert assesment question
		$this->_insert_assessment_question($activeYear, $jobtitle);
	}

	/**
	 * Insert assessment question
	 * @param string $activeYear
	 * @param int $jobtitle
	 * @return null
	 */
	private function _insert_assessment_question(string $activeYear, int $jobtitle)
	{
		$getForm = $this->assessment->is_assessment_form_exist($activeYear, $jobtitle);
		foreach ($getForm as $form) {
			// check is assessment question form has exist
			$isQuestionExist = $this->db->where('form_id', $form->id)->get('assessment_form_question');

			// if question doesn't exist, create it
			if ($isQuestionExist->num_rows() < 1) {
				$questionCompetency = $this->assessmentForm->create_assessment_question($form->job_id);

				foreach ($questionCompetency->result() as $competency) {
					$assessmentQuestion[] = [
						'form_id' => $form->id,
						'skill_unit_id' => $competency->unit_id,
						'weight' =>$competency->bobot
					];
				}
			}
		}
		$this->db->insert_batch('assessment_form_questions', $assessmentQuestion);
		return;
	}
	

}

/* End of file Assessment.php */
/* Location: ./application/modules/assessment/controllers/Assessment.php */