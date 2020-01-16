<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

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

        $this->load->model('dashboard_model','dashboard');
    }

	public function index()
	{
        $this->load->model('manage_model','manage');
        $data['informations'] = $this->manage->get_information()->result();
        $data['group']   = $this->group;
        $data['section'] = $this->section;
        // for login as admin or HR
        if ($this->group == 1 || $this->group == 2) {
            $data['participants']             = $this->dashboard->get_participants()->num_rows();
            $data['assessmentThatUncomplete'] = $this->dashboard->uncomplete();
            $data['completedAssessment']      = $this->dashboard->complete();

        // for login as non admin or non HR
        } else {
            $data['participants'] = $this->dashboard->get_participants($this->section)->num_rows();
            $data['assessmentThatUncomplete'] = $this->dashboard->uncomplete(TRUE, $this->section);
            $data['completedAssessment']      = $this->dashboard->complete(TRUE, $this->section);
        }

        $data['uncompletePercentage'] = ($data['assessmentThatUncomplete']/$data['participants']) * 100;
        $data['completePercentage']   = ($data['completedAssessment']/$data['participants']) * 100;

		$data['page'] = "dashboard_v";
		$this->load->view('template/template', $data);
	}

    /**
     * Get content of dashboard chart -- for job title
     * @param string $adminOrHR
     * @param int $section
     * @return void
     */
    public function jobtitle_chart(string $adminOrHR='true', int $section=0) : void
    {
        if ($adminOrHR == 'true') {
            $datas = $this->dashboard->employe_per_jobtitle();    
        } else {
            $datas = $this->dashboard->employe_per_jobtitle(false, $section);
        }
        
        foreach ($datas as $data) {
            $object[] = [
                'name' => $data->job_title,
                'y' => $data->amount
            ];
        }

        echo json_encode($object);
    }

    /**
     * Get ratio from number of employes that grouped by their grade
     * @param string $adminOrHR
     * @param int $section
     * @return void
     */
    public function employes_grade(string $adminOrHR='true', int $section=0) : void
    {
        if ($adminOrHR == 'true') {
            $datas = $this->dashboard->employe_per_grade();    
        } else {
            $datas = $this->dashboard->employe_per_grade(false, $section);
        }

        foreach ($datas as $data) {
            $object[] = [
                'name' => 'Level '.$data->level,
                'y' => $data->amount
            ];
        }

        echo json_encode($object);
    }

    /**
     * See detail of complete and uncomplete assessment
     * @param string $status
     * @param int $section
     * @return void
     */
    public function see_detail(string $status, int $section=0) : void
    {
        if ($status == 'ALL_PARTICIPANTS') {
            $data['pageTitle'] = "All Participants";
            // for login as admin/HR
            if ($section == 0) {
                $data['employes'] = $this->dashboard->get_participants_detail();
            // for login as non admin/HR
            } else {
                $data['employes'] = $this->dashboard->get_participants_detail($section);
            }
            
        } elseif ($status == 'UNCOMPLETE') {
            $data['pageTitle'] = "Uncomplete Participants";
            // for login as admin/HR
            if ($section == 0) {
                $data['employes'] = $this->dashboard->uncomplete_employes(TRUE);
            // for login as non admin/HR
            } else {
                $data['employes'] = $this->dashboard->uncomplete_employes(FALSE,$section);
            }
            
        } else {
            $data['pageTitle'] = "Complete Participants";
            // for login as admin/HR
            if ($section == 0) {
                $data['employes'] = $this->dashboard->complete_detail(TRUE);
            // for login as non admin/HR
            } else {
                $data['employes'] = $this->dashboard->complete_detail(FALSE, $section);
            }
                        
        }

        $data['page'] = "detail_assessment_v";
        $this->load->view('template/template', $data);
	}

}

/* End of file Dashboard.php */
/* Location: ./application/modules/dashboard/controllers/Dashboard.php */