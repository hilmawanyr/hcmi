<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment extends CI_Controller {

	private $nik, $group, $level, $grade, $section, $department, $position, $position_grade;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('login_session')) {
			redirect(base_url('logout'));
		}

		$loginSession = $this->session->userdata('login_session');
        $this->nik            = $loginSession['nik'];
        $this->group          = $loginSession['group'];
        $this->level          = $loginSession['level'];
        $this->grade          = $loginSession['grade'];
        $this->section        = $loginSession['section'];
        $this->department     = $loginSession['department'];
        $this->position       = $loginSession['position'];
        $this->position_grade = $loginSession['position_grade'];

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
                // if AM or SAM
                if ($this->position_grade > 3 && $this->position_grade < 7) {
                    $getJobtitleList = $this->assessment->jobtitle_by_grade_and_section(3, $this->section);
                } elseif ($this->position_grade > 6 && $this->position_grade < 9) {
                    $getJobtitleList = $this->assessment->jobtitle_by_grade_and_department(3, $this->department);
                } elseif ($this->position_grade > 8) {
                    $getJobtitleList = $this->assessment->jobtitle_by_director(3, $this->department);
                }
				
				break;
		}

        $data['position_grade'] = $this->position_grade;
        $data['position']       = $this->position;
        $data['department']     = $this->department;
        $data['section']        = $this->section;
        $data['jobtitleList']   = $getJobtitleList;
        $data['page']           = 'assessment_v';
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

		$data['department'] = get_department_by_section($data['sectionId'])->id;

		$get_employes = $this->db->get_where('employes',['job_title_id' => $jobtitle]);
		$data['jobTitleName'] = $this->db->where('id', $jobtitle)->get('job_titles')->row();

		// load competency
        $data['dictionary'] = $this->assessment->get_competency($jobtitle);

        // Taruh pengecheckan kalo matrix belum ada
        if ($data['dictionary']->num_rows() < 1) {
            $this->session->set_flashdata('fail_save_data', 'Matriks kompetensi tidak ditemukan , mohon lengkapi terlebih dahulu matriks kompetensi untuk job posisi tersebut!');
            redirect('assessment','refresh');
        }

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

		// value for upload form
		$data['job_title'] = $jobtitle;
        $data['form_code'] = 'AF-'.$jobtitle.'-'.$data['active_year'];

        $data['position_code']  = $this->db->query("SELECT pos.code FROM employes em 
                                                    JOIN positions pos ON  em.position_id = pos.id
                                                    WHERE em.nik = '$this->nik'")->row()->code;

        $data['assessment_state'] = $this
                                        ->db
                                        ->get_where('assessment_form_state', ['code_form' => $data['form_code']])
                                        ->row()
                                        ->state;
        
		// number of filled assessment
		$data['completeAssessment'] = $this->assessment->complete_assessment($jobtitle);
		$data['employes'] = $get_employes;
		// $data['page'] = 'assessment_form_v';
        $data['page'] = 'assessment_form_fill';
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
                $state = $this->_get_workflow_state();

				// create an array of assessment form data to make insert batch
				$assessmentForm[] = [
                    'code'   => 'AF-'.$employe->job_title_id.'-'.$activeYear,
                    'nik'    => $employe->nik,
                    'job_id' => $employe->job_title_id,
				];

                $assessmentState = [
                    'code_form' => 'AF-'.$employe->job_title_id.'-'.$activeYear,
                    'state'     => $state
                ];
			}
		}
		$this->db->insert_batch('assessment_forms', $assessmentForm);

        // insert to assesment state
        $this->db->insert('assessment_form_state', $assessmentState);

		// insert assesment question
		$this->_insert_assessment_question($activeYear, $jobtitle);
		return;
	}

    /**
     * Set workflow state when generate form
     * 
     * @return void
     */
    private function _get_workflow_state(string $state="")
    {
        $level = 0;
        if ($state != "") {
            $assment_state = $this->db->query("SELECT * FROM workflow_state WHERE state = '$state' LIMIT 1")->row();
            $level = $assment_state->level;
        }

        $states = $this->db->query("SELECT * FROM workflow_state WHERE level > '$level' ORDER BY level ASC")->result();
        foreach ($states as $value) {
            $check  = $this->db->query("SELECT em.position_id FROM employes em 
                                        JOIN positions pos ON em.position_id = pos.id
                                        WHERE em.dept_id = '$this->department'
                                        AND pos.code = '$value->state'
                                        GROUP BY em.position_id")->num_rows();
            if ($check >= 1) {
                return $value->state;
            }
        }
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
        $id_form        = $this->input->post('idform');
        $inputamount    = count($this->input->post('nilai_mentah'));
        $limitEmptyPoin = $inputamount-1;

        // prevent if user fill with empty poin for all statement
        if (count(array_unique($this->input->post('nilai_mentah'))) == 1) {
        	$this->session->set_flashdata('fail_save_data', 'Gagal menyimpan data! Minimal mengisi satu pernyataan!');
			redirect(base_url('form/'.$this->input->post('job')));
        }

        // prevent if poin that inputed > 1
        $checkEmptyArray = array_filter($this->input->post('nilai_mentah'), function ($val) {
        	return $val == "";
        });

        if (count($checkEmptyArray) < $limitEmptyPoin) {
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

        // set to max poin for each competency that under filled competency
        $filledArrayIndex = array_search($assessedCompetency, $poin);
        for ($n = 0; $n < $filledArrayIndex; $n++) {
        	$poin[$n][0] = 5;
        }

        // prevent for freak input after edit
        $dictionaryId = $this->db->where('id', $assessedCompetency[1])->get('skill_units')->row()->id_dictionary;
        // set poin to null
        $this->db->query("UPDATE assessment_form_questions SET poin = NULL 
						WHERE form_id =  $id_form
						AND skill_unit_id IN (SELECT id FROM skill_units WHERE id_dictionary = $dictionaryId)");

        for ($j = 0; $j <= $filledArrayIndex; $j++) {
        	$data = ['poin' => $poin[$j][0]];
	        $this->db->where('form_id', $id_form);
	        $this->db->where('skill_unit_id', $poin[$j][1]);
	        $this->db->update('assessment_form_questions', $data);
        }
        // $data = ['poin' => $assessedCompetency[0]];
        // $this->db->where('form_id', $id_form);
        // $this->db->where('skill_unit_id', $assessedCompetency[1]);
        // $this->db->update('assessment_form_questions', $data);

        /**
         * if amount of statement for each job title is not equal
         * with amount of filled statement
         * so total_poin in assessment_forms will not filled 
         */
        $assessForm = $this->db->where('id', $this->input->post('idform'))->get('assessment_forms')->row();
        $jobTitle = $assessForm->job_id;

        /** amount of statement base on job title */
        $statementAmountbyJobtitle = $this->db
        									->where('job_id', $jobTitle)
    										->where('deleted_at')
    										->get('skill_matrix')
    										->num_rows();

        /** check amount of filled statement  */
        $filledFormAmount = $this->db
        							->where('form_id', $this->input->post('idform'))
        							->where('poin is NOT NULL', NULL, FALSE)
        							->get('assessment_form_questions');

        $filledStatementPerDictionary = $this->db
			        							->select('COUNT(distinct b.id_dictionary) AS totalFilled')
			        							->from('assessment_form_questions a')
			        							->join('skill_units b','a.skill_unit_id = b.id')
			        							->where('a.form_id',$id_form)
			        							->where('a.poin IS NOT NULL', NULL, FALSE)
			        							->get()->row()->totalFilled;


        /**
         * if amount of statement base on job title is equal with amount of filled statement
         * so update total_poin in assessment_forms
        */
        if ($statementAmountbyJobtitle == $filledStatementPerDictionary) {
            $const = 0;
            foreach ($filledFormAmount->result() as $val) {
                $const = $const + ($val->weight / 5) * $val->poin;
            }
            $totalPoin = $const;
            $grade = get_assessment_grade($totalPoin);
            $this->db->where('id', $this->input->post('idform'))
                    ->update('assessment_forms',
                        [
                            'total_poin' => $totalPoin, 
                            'poin_grade' => $grade,
                            'audit_by'   => $this->session->userdata('login_session')['nik']
                        ]);
        /**
         * but if total_poin in assessment_forms has filled cause intentionally submit
         * update it to NULL
         */
        } elseif ($statementAmountbyJobtitle > $filledStatementPerDictionary) {
            $isTotalPoinNull = $this->db->where('id', $this->input->post('idform'))->get('assessment_forms')->row();
            if (!is_null($isTotalPoinNull->total_poin)) {
            	$this->db
            			->where('id', $this->input->post('idform'))
            			->update('assessment_forms',['total_poin' => NULL]);
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
        } elseif ($this->group == 2 && $this->level == 3) {
        	$flag = 3; // for manager in HR
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

    public function submit_form_2(int $jobtitleId) : void
    {
        $code_form            = 'AF-'.$jobtitleId.'-'.get_active_year();
        $get_assessment_state = $this->db->get_where('assessment_form_state',  ['code_form' => $code_form])->row();
        $state                = $this->_get_workflow_state($get_assessment_state->state);

        if ($state == 'GM' ||  $state == 'DIR') {
            $state = 'DONE';
        }

        $this->db->where('code_form', $code_form);
        $this->db->update('assessment_form_state', ['state' => $state]);
        redirect(base_url('form/'.$jobtitleId));
    }
    /**
     * Export assessment for to excel
     * @param int $jobtitleId
     * @return void
     */
    public function export_assessment_to_excel(int $jobtitleId)
    {
        $this->load->library('excel');
        // active assessment year
        $data['activeyear']     = get_active_year();

        // job titlle id
        $data['jobtitle'] = $jobtitleId;
        
        // get job title name
        $data['jobtitlename']   = $this->db->where('id', $jobtitleId)->get('job_titles')->row();
        
        // load competency base on job title
        $data['dictionary']     = $this->assessment->get_assessment_matrix($jobtitleId);
        
        $data['numberofcolumn'] = count($data['dictionary']) + 3;
        
        // get emlployee base on job title
        $data['employee']       = $this->db->where('job_title_id', $jobtitleId)->get('employes')->result();
        
        $this->load->view('excel_assessment_form2', $data);
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
	
	/**
     * Handle import assessment form
     */
    public function upload()
    {
		$this->load->model("dictionary_competancy", "dc");

		$job_title_id = $this->input->post('job_title_id');
		$form_code = $this->input->post('form_code');

		$fileName = time().$_FILES['userfile']['name'];
		$path_upload='./assets/excel/assessment/';
         
        $config['upload_path'] = $path_upload; //buat folder dengan nama assets di root folder
        $config['file_name'] = $fileName;
        $config['allowed_types'] = '*';
		$config['max_size'] = 10000;
		
		$this->load->library('upload', $config);
         
        if (!$this->upload->do_upload('userfile'))
		{
				$error = array('error' => $this->upload->display_errors());
				die($error);
		}
		else
		{
				$media = $this->upload->data();

				$this->load->library('PHPExcel');
				$tmpfname = $media['full_path'];
				
				
				
				try {
					$excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
					$excelObj = $excelReader->load($tmpfname);
					$worksheet = $excelObj->getSheet(0);
					$lastRow = $worksheet->getHighestRow();

					$id_form = $worksheet->getCell('A1')->getValue();
					$nik = $worksheet->getCell('A4')->getValue();
					
					$k1 = $worksheet->getCell('C3')->getValue();
					$id_k1 = $this->dc->get_dictionary_by_name($k1)->id;

					$k2 = $worksheet->getCell('C4')->getValue();
					$id_k2 = $this->dc->get_dictionary_by_name($k2)->id;
					
					$k3 = $worksheet->getCell('C5')->getValue();
					$id_k3 = $this->dc->get_dictionary_by_name($k3)->id;

					$k4 = $worksheet->getCell('C6')->getValue();
					$id_k4 = $this->dc->get_dictionary_by_name($k4)->id;

					$k5 = $worksheet->getCell('C7')->getValue();
					$id_k5 = $this->dc->get_dictionary_by_name($k5)->id;

					for ($row = 1; $row <= $lastRow; $row++) {
						$letter = 'A';
						for ($col = 1; $col <= 8; $col++ ){

							if ($row >= 4 && $letter == 'C') {
								$k = $row - 3;

								$poin = $worksheet->getCell($letter.$row)->getValue();

								$this->db->query("UPDATE assessment_form_questions SET poin = $poin 
													WHERE form_id =  $id_form
													AND skill_unit_id IN (SELECT id FROM skill_units WHERE id_dictionary = $dictionaryId)");
								
							}
							
							$letter++;
						}
				   	}
					
				} catch (\Throwable $th) {
					die($th);
				}
		}

		redirect(base_url('form/'.$job_title_id));
    }

    function ldap()
    {
        $data = [
            '23310566',
            '70042890',
            '22389854',
            '22661244',
            '22388583',
            '22391927',
            '22395246',
            '22391597',
            '22389409',
            '24142277',
            '70362761',
            '70470211',
            '23311628',
            '24104598',
            '24104618',
            '24142204',
            '24142205',
            '24142206',
            '24142207',
            '24142210',
            '24142211',
            '24142212',
            '24142223',
            '24142224',
            '24142227',
            '24142229',
            '24142231',
            '24142232',
            '24142234',
            '24142235',
            '24142236',
            '24142237',
            '24142243',
            '24142256',
            '24142260',
            '24142261',
            '24142262',
            '24142268',
            '24142269',
            '24142270',
            '24142273',
            '24142274',
            '24142280',
            '24142281',
            '24142287',
            '24142288',
            '24142291',
            '24142293',
            '24142294',
            '24142296',
            '24142297',
            '24142298',
            '24142301',
            '24142306',
            '24142317',
            '24142324',
            '24142326',
            '24142340',
            '24142348',
            '24142350',
            '24142357',
            '70148878',
            '70148881',
            '70353484',
            '70353485',
            '70353487',
            '70353494',
            '70470169',
            '70470178',
            '70470179',
            '70470181',
            '70470185',
            '70470204',
            '70470207',
            '70470209',
            '70470216',
            '70470219',
            '70470224',
            '70470225',
            '70470235',
            '70470236',
            '70470239',
            '70470243',
            '70470247',
            '70470267',
            '70470274',
            '70470276',
            '70470285',
            '70470295',
            '70470297',
            '70470299',
            '70470303',
            '70508230',
            '71499589',
            '22390125',
            '22273062',
            '24142217',
            '24142230'
        ];

        foreach ($data as $val) {
            $ldap[] = [
                'ldap_id' => $val
            ];
        }

        var_dump($ldap); exit();

        $this->db->insert_batch('users', $ldap);
    }

}

/* End of file Assessment.php */
/* Location: ./application/modules/assessment/controllers/Assessment.php */