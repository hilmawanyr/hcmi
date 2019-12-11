<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function index()
	{
		$data['page'] = "dashboard_v";
		$this->load->view('template/template', $data);
	}

	public function ratioFormFelling($status){
		$UserData = $this->session->userdata('login_session');
		$sectionByNik = get_section_by_nik($UserData['nik']);
		$activeYear = get_active_year();

		if ($status == 1) {
            if ($UserData['group_id'] == 1 || $UserData['group_id'] == 2) {
                $ratio = DB::table("assessment_forms")
                            ->where("code","LIKE", "%".$activeFormYear)
                            ->whereNotNull("total_poin")
                            ->count();
            } else {
                $ratio = DB::table("assessment_forms")
                            ->where("code","LIKE", "%".$activeFormYear)
                            ->whereIn('job_id',[DB::raw('SELECT id FROM job_titles where section = '.$sectionByNik.'')])
                            ->whereNotNull("total_poin")
                            ->count();
            }

        // for participants who have not complete the assessed
        } else {
            if ($UserData['group_id'] == 1 || $UserData['group_id'] == 2) {
                $employeHaveNotAssessed = DB::table('employes')
                                            ->whereNotIn('nik',function ($query){
                                                $query->select('nik')
                                                ->from('assessment_forms')
                                                ->where('code','LIKE','%'.Helper::yearActiveForm());
                                            })
                                            ->where('employes.name','<>','admin')
                                            ->whereNotIn('employes.position_id',[7,8,9])
                                            ->count();

                $employeHaveNotComplete = DB::table('assessment_forms')
                                            ->whereNull('total_poin')
                                            ->where('code','LIKE','%'.$activeFormYear)
                                            ->count();
            } else {
                $employeHaveNotAssessed = DB::table('employes')
                                            ->whereNotIn('nik',function ($query){
                                                $query->select('nik')
                                                ->from('assessment_forms')
                                                ->where('code','LIKE','%'.Helper::yearActiveForm());
                                            })
                                            ->whereIn('job_title_id',[DB::raw('SELECT id FROM job_titles where section = '.$sectionByNik.'')])
                                            ->where('employes.name','<>','admin')
                                            ->whereNotIn('employes.position_id',[7,8,9])
                                            ->count();

                $employeHaveNotComplete = DB::table('assessment_forms')
                                            ->whereNull('total_poin')
                                            ->where('code','LIKE','%'.$activeFormYear)
                                            ->whereIn('job_id',[DB::raw('SELECT id FROM job_titles where section = '.$sectionByNik.'')])
                                            ->count();
            }
            $ratio = $employeHaveNotAssessed + $employeHaveNotComplete;
        }
	}

}

/* End of file Dashboard.php */
/* Location: ./application/modules/dashboard/controllers/Dashboard.php */