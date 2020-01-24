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
		$data['section']      = $this->section;
		$data['jobtitleList'] = $getJobtitleList;
		$data['page']         = 'assessment_v';
		$this->load->view('template/template', $data);
	}

	/**
	 * Show all employes by their jobtitle
	 * @param int $jobtitle
	 * @return void
	 */
	public function form(string $jobtitle) : void
	{
		$data['active_year'] = get_active_year();

		$data['sectionId'] = get_section_by_jobtitle($jobtitle);

		$get_employes = $this->db->where('job_title_id', $jobtitle)->get('employes');
		$data['jobTitleName'] = $this->db->where('id', $jobtitle)->get('job_titles')->row();

		// load competency
		$data['dictionary'] = $this->assessment->get_competency($jobtitle);

		// check whether assessment form has generate or not
		$isFormExist = $this->assessment->is_assessment_form_exist($data['active_year'], $jobtitle);

		// generate form if job title has competency
		if ($data['dictionary']->num_rows() > 0) {
			// insert form assessment if those doesn't exist
			if ($isFormExist->num_rows() < 1) {
				$this->_generate_form_assessment($get_employes->result(), $jobtitle);
			}

			// change value of $get_employe if job_titles has competency matrixes
			$get_employes = $this->assessment->competency_by_jobtitle($data['active_year'], $jobtitle);
		}

		// check whether form has submited or not
		$data['isSubmited'] = $this->db->like('code', 'AF-'.$jobtitle.'-'.$data['active_year'], 'BOTH')
										->get('assessment_validations')
										->num_rows();

		if ($data['isSubmited'] > 0) {
			$data['submitStatus'] = $this->db->where('code', 'AF-'.$jobtitle.'-'.$data['active_year'])->get('assessment_validations')->row()->is_valid;
		} else {
			$data['submitStatus'] = 0;
		}

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
		return;
	}

	/**
	 * Insert assessment question
	 * @param string $activeYear
	 * @param int $jobtitle
	 * @return null
	 */
	private function _insert_assessment_question(string $activeYear, int $jobtitle)
	{
		$getForm = $this->assessment->is_assessment_form_exist($activeYear, $jobtitle)->result();
		foreach ($getForm as $form) {
			// check is assessment question form has exist
			$isQuestionExist = $this->db->where('form_id', $form->id)->get('assessment_form_questions');

			// if question doesn't exist, create it
			if ($isQuestionExist->num_rows() < 1) {
				$questionCompetency = $this->assessment->create_assessment_question($form->job_id);

				foreach ($questionCompetency as $competency) {
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
	
	/**
	 * Get competency for each dictionary
	 * @param string $nik
	 * @param int $jobtitle 
	 * @param int $skillId
	 * @return void
	 */
	public function get_competency(string $nik, int $jobtitle, int $skillId) : void
    {
        $activeYear = get_active_year();
        $data['dict'] = $skillId;
        $data['nik'] = $nik;
        $data['job'] = $jobtitle;

        $competency = $this->assessment->get_competency_for_assessment($nik, $jobtitle, $skillId, $activeYear);

        $data['dictionary'] = $this->db->where('id', $skillId)->get('skill_dictionaries')->row();
        
        // get employe name
        $data['employname'] = $this->db->where('nik', $nik)->get('employes')->row();
        $data['competency'] = $competency;
        $this->load->view('assessment_modal_v', $data);
    }

    /**
     * Store assessment point to DB
     * 
     * @return void
     */
    public function insert_poin() : void
    {
    	$id_form = $this->input->post('idform');
        $inputamount = count($this->input->post('nilai_mentah'));

        // prevent if poin that inputed > 1
        $checkEmptyArray = array_filter($this->input->post('nilai_mentah'), function ($val) {
        	return $val == "";
        });

        if (count($checkEmptyArray) < 4) {
        	$this->session->set_flashdata('fail_save_data', 'Gagal menyimpan data! Hanya boleh mengisi satu pernyataan');
			redirect(base_url('form/'.$this->input->post('job')));
        }
        // prevent end

        for ($i=0; $i < $inputamount; $i++) {
        	// prevent poin that bigger than 5
        	if ($this->input->post('nilai_mentah')[$i] > 5) {
        		$this->session->set_flashdata('fail_save_data', 'Gagal menyimpan data! Nilai tidak boleh lebih dari 5');
				redirect(base_url('form/'.$this->input->post('job')));
        	}
            // compulate data in array 2 dimension
            $poin[] = [$this->input->post('nilai_mentah')[$i],$this->input->post('skill_id')[$i]];
        }

        // get poin based on assessed competency
        $assessedCompetency = max($poin);

        // prevent for freak input after edit
        $dictionaryId = $this->db->where('id', $assessedCompetency[1])->get('skill_units')->row()->id_dictionary;
        // set poin to null
        $this->db->query("UPDATE assessment_form_questions SET poin = NULL 
						WHERE form_id =  $id_form
						AND skill_unit_id IN (SELECT id FROM skill_units WHERE id_dictionary = $dictionaryId)");

        $data = ['poin' => $assessedCompetency[0]];
        $this->db->where('form_id', $id_form);
        $this->db->where('skill_unit_id', $assessedCompetency[1]);
        $this->db->update('assessment_form_questions', $data);

        /**
         * if amount of statement for each job title is not equal
         * with amount of filled statement
         * so total_poin in assessment_forms will not filled 
         */
        $assessForm = $this->db->where('id', $this->input->post('idform'))->get('assessment_forms')->row();
        $jobTitle = $assessForm->job_id;

        /** amount of statement base on job title */
        $statementAmountbyJobtitle = $this->db->where('job_id', $jobTitle)->where('deleted_at')->get('skill_matrix')->num_rows();

        /** check amount of filled statement  */
        $filledFormAmount = $this->db->where('form_id', $this->input->post('idform'))->where('poin is NOT NULL', NULL, FALSE)->get('assessment_form_questions');

        /**
         * if amount of statement base on job title is equal with amount of filled statement
         * so update total_poin in assessment_forms
        */
        if ($statementAmountbyJobtitle == $filledFormAmount->num_rows()) {
            $const = 0;
            foreach ($filledFormAmount->result() as $val) {
                $const = $const + ($val->poin * $val->weight);
            }
            $totalPoin = $const;
            $this->db->where('id', $this->input->post('idform'))->update('assessment_forms',['total_poin' => $totalPoin, 'audit_by' => $this->session->userdata('login_session')['nik']]);
        /**
         * but if total_poin in assessment_forms has filled cause intentionally submit
         * update it to NULL
         */
        } elseif ($statementAmountbyJobtitle > $filledFormAmount->num_rows()) {
            $isTotalPoinNull = $this->db->where('id', $this->input->post('idform'))->get('assessment_forms')->row();
            if (!is_null($isTotalPoinNull->total_poin)) {
            	$this->db->where('id', $this->input->post('idform'))->update('assessment_forms',['total_poin' => NULL]);
            }
        }

        redirect(base_url('form/'.$this->input->post('job')));
    }

    /**
     * Handle submit form assessment
     * @param int $jobtitleId
     * @return void
     */
    public function submit_form(int $jobtitleId) : void
    {
    	// set validation flag
    	if ($this->group == 3 && $this->level == 1) {
    		$flag = 1; // for asistant manager
    	} elseif($this->group == 3 && $this->level == 2) {
    		$flag = 2; // for manager
    	} elseif ($this->group == 3 && $this->level == 3) {
    		$flag = 3; // for GM
    	} elseif ($this->group == 2 && $this->level == 2) {
            $flag = 2; // for manager in HR
        }
        // get active year of assessment
        $activeyear = get_active_year();

        // is validation exist ?
        $isValidationExist = $this->db->where('code', 'AF-'.$jobtitleId.'-'.$activeyear)->get('assessment_validations')->num_rows();

        if ($isValidationExist > 0) {
        	$this->db->where('code', 'AF-'.$jobtitleId.'-'.$activeyear)->update('assessment_validations', ['is_valid' => $flag]);
        } else {
        	$this->db->insert('assessment_validations', ['code' => 'AF-'.$jobtitleId.'-'.$activeyear, 'is_valid' => $flag]);	
        }

        redirect(base_url('form/'.$jobtitleId));
    }

    /**
     * Export assessment for to excel
     * @param int $jobtitleId
     * @return void
     */
    public function export_assessment_to_excel(int $jobtitleId)
    {
        // active assessment year
        $data['activeyear']    = get_active_year();

        // get job title name
        $data['jobtitlename']  = $this->db->where('id', $jobtitleId)->get('job_titles')->row();

        // load competency base on job title
        $data['dictionary']    = $this->assessment->get_assessment_matrix($jobtitleId);

        $data['numberofcolumn']= count($data['dictionary']) + 3;

        // get emlployee base on job title
        $data['employee']      = $this->db->where('job_title_id', $jobtitleId)->get('employes')->result();
        
        $this->load->view('excel_assessment_form', $data);
    }
    
	/**
	 * Look up each assessment poin from HR
	 * @param int $skillId
	 * @param string $nik
	 * @return void
	 */
	public function see_poin(int $skillId, string $nik, int $jobId) : void
    {
        // $data['dictionary']= $this->db->where('id', $skillId)->get('skill_dictionaries')->row();
        // $data['nik']       = $nik;
        // $data['poin']      = $this->assessment->get_poin($skillId, $nik);

        // $this->load->view('assessment_modal_view_poin', $data);

        $activeYear = get_active_year();
        $data['dict'] = $skillId;
        $data['nik'] = $nik;
        $data['job'] = $jobId;

        $competency = $this->assessment->get_competency_for_assessment($nik, $jobId, $skillId, $activeYear);

        $data['dictionary'] = $this->db->where('id', $skillId)->get('skill_dictionaries')->row();
        
        // get employe name
        $data['employname'] = $this->db->where('nik', $nik)->get('employes')->row();
        $data['competency'] = $competency;
        $this->load->view('assessment_modal_view_poin2', $data);
    }

    /**
     * Load competency description in table header of assessment
     * @param int $id
     * @return void
     */
    public function competency_description(int $id) : void
    {
		$data['description'] = $this->db->where('id', $id)->get('skill_dictionaries')->row();
		$this->load->view('competency_description_modal', $data);    	
    }

}

/* End of file Assessment.php */
/* Location: ./application/modules/assessment/controllers/Assessment.php */