<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment_year extends CI_Controller {

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

		$this->load->model('manage_model','manage');
	}

	public function index()
	{
		$data['years'] = $this->manage->get_assessment_year();
		$data['page']  = "assessment_year_v";
		$this->load->view('template/template', $data);
	}

	/**
	 * Create new assessment year data
	 * 
	 * @return void
	 */
	public function store()
	{
		$year     = $this->input->post('year');
		$isActive = $this->input->post('isActive');
		$isUpdate = $this->input->post('isUpdate');

		/* check is same name exist? */
		$isYearNameExist = $this->_is_year_name_exist($year, $isUpdate);

		/* if new assessment year data has active year property with value 1, check to db before insert */
		if ($isActive == '1') {
			$isThereActiveYear = $this->_is_there_active_year();
		}
		
		$storedData = [
			'year'      => $year,
			'is_active' => $isActive
		];

		if ($isUpdate == '') {
			$this->db->insert('assessment_years', $storedData);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
		} else {
			/* check whether assessment process is ongoing. if true, edit can not doing */
			$yearName = $this->db->where('id', $isUpdate)->get('assessment_years')->row()->year;
			$this->_is_assessment_ongoing($yearName);
			
			$this->db->where('id', $isUpdate);
			$this->db->update('assessment_years', $storedData);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
		}
		redirect(base_url('assessment_year'));
	}

	/**
	 * Check is any active year in db ?
	 * 
	 * @return void
	 */
	private function _is_there_active_year() : void
	{
		$isThereActiveYear = $this->db->where('is_active', 1)->get('assessment_years',1)->num_rows();
		if ($isThereActiveYear == 1) {
			$this->session->set_flashdata('fail_save_data', 'Data not saved. Only one active period in the database is allowed!');
			redirect('assessment_year');
		}
	}

	/**
	 * Check is year name exist ?
	 * @param int $name
	 * @return void
	 */
	private function _is_year_name_exist(int $name, $isUpdate) : void
	{
		$isThereActiveYear = $this->db->where('year', $name)->get('assessment_years',1)->num_rows();
		if ($isThereActiveYear == 1) {

			// is 'update' or 'save' activity?
			if ($isUpdate == '') {
				$this->session->set_flashdata('fail_save_data', 'Data not saved. The same year name has exist!');	
			} else {
				$this->session->set_flashdata('fail_save_data', 'Data not updated. The same year name has exist!');
			}
			
			redirect('assessment_year');
		}
		return;
	}

	/**
	 * Validation when edit year name. If year name in use, edit cannot doing
	 * @param string $name
	 * @return void
	 */
	private function _is_assessment_ongoing(string $name) : void
	{
		$isYearNameInUse = $this->db->like('code', $name, 'before')->get('assessment_forms')->num_rows();
		if ($isYearNameInUse > 0) {
			$this->session->set_flashdata('fail_save_data', 'Data can not update. Year is in use!');
			redirect('assessment_year');
		}
		return;
	}

	/**
	 * Edit assessment year data
	 * @param int $id
	 * @return void
	 */
	public function edit(int $id) : void
	{
		$assessmentYear = $this->manage->get_assessment_year_detail($id);
		$response = [
			'name' => $assessmentYear->year,
			'id'   => $assessmentYear->id
		];
		echo json_encode($response);
	}

	/**
	 * Change active year of assessment
	 * @param int $id
	 * @return void
	 */
	public function set_active_year(int $id) : void
	{
		$this->db->update('assessment_years', ['is_active' => NULL]);

		$this->db->where('id', $id);
		$this->db->update('assessment_years', ['is_active' => 1]);
		$this->session->set_flashdata('success_change_active_year', 'Active year successfully change!');
		redirect('assessment_year');
	}	

	/**
	 * Remove assessment year
	 * @param int $id
	 * @return void
	 */
	public function remove(int $id) : void
	{
		// check whether has used by assessment form
		$this->_is_year_has_used($id);

		// check whether year is active
		$this->_is_year_active($id);

		// remove year
		$this->db->where('id', $id)->delete('assessment_years');
		$this->db->where('year_id', $id)->delete('assessment_settings');
		$this->session->set_flashdata('success_remove_data', 'Data removed!');
		redirect('assessment_year');
	}

	/**
	 * Check whether year has been used by assessment form
	 * @param int $id
	 * @return void
	 */
	private function _is_year_has_used(int $id) : void
	{
		$getYearById = $this->manage->get_assessment_year_detail($id)->year;

		$isYearHasUsed = $this->db->like('code', $getYearById, 'before')->get('assessment_forms')->num_rows();
		if ($isYearHasUsed > 0) {
			$this->session->set_flashdata('fail_remove_data', 'Can not remove assessment year. Year has used by assessment!');
			redirect('assessment_year');
		}
		return;
	}

	/**
	 * Check whether year is active
	 * @param int $id
	 * @return void
	 */
	private function _is_year_active(int $id) : void
	{
		$isYearActive = $this->db->where('id', $id)->get('assessment_years')->row();
		if (!is_null($isYearActive->is_active)) {
			$this->session->set_flashdata('fail_remove_data', 'Can not remove assessment year. Year is active!');
			redirect('assessment_year');
		}
		return;
	}

	/**
	 * Seting periode for each assessment year
	 * 
	 * @return void
	 */
	public function set_period() : void
	{
		$yearId       = $this->input->post('yearId');
		$yearName     = $this->input->post('yearName');
		$startDate    = $this->input->post('startDate');
		$endDate      = $this->input->post('endDate');
		$yearOfPeriod = explode('-', $startDate)[0];

		$isPeriodValid = $this->_is_period_valid($yearName, $yearOfPeriod, $startDate, $endDate);

		$isPeriodExist = $this->_is_period_exist($yearId);

		$storedData = [
			'year_id' => $yearId,
			'start_date' => $startDate,
			'end_date' => $endDate
		];

		if ($isPeriodExist > 0) {
			$this->db->where('year_id', $yearId)->update('assessment_settings', $storedData);
			$this->session->set_flashdata('success_update_data', 'Update successfully!');
			redirect('assessment_year');
		} else {
			$this->db->insert('assessment_settings', $storedData);
			$this->session->set_flashdata('success_save_data', 'Saved successfully!');
			redirect('assessment_year');	
		}
	}

	/**
	 * Chech whether period is exist
	 * @param int $id
	 * @return int
	 */
	private function _is_period_exist(int $yearId) : int
	{
		return $this->db->where('year_id', $yearId)->get('assessment_settings',1)->num_rows();
	}

	/**
	 * Check validity of assessment year periode
	 * @param int $id
	 * @return int
	 */
	private function _is_period_valid(string $year, string $period, string $startDate, string $endDate) : void
	{
		// is periode with assessment year is match?
		if ($year != $period) {
			$this->session->set_flashdata('fail_save_data', 'Can not save period. Year and period do not match!');
			redirect('assessment_year');
		}

		// do range valid ?
		if ($startDate > $endDate || $endDate < $startDate) {
			$this->session->set_flashdata('fail_save_data', 'Can not save period. Period range not valid!');
			redirect('assessment_year');
		}

		return;
	}

	/**
	 * Load detail periode
	 * @param int $id
	 * @return void
	 */
	public function detail_period(int $id) : void
	{
		$detailPeriode = $this->db->where('year_id', $id)->get('assessment_settings',1);
		if ($detailPeriode->num_rows() > 0) {
			$response = [
				'year_id' => $detailPeriode->row()->year_id,
				'start_date' => $detailPeriode->row()->start_date,
				'end_date' => $detailPeriode->row()->end_date
			];	
		} else {
			$response = [
				'year_id' => '',
				'start_date' => '',
				'end_date' => ''
			];
		}
		
		echo json_encode($response);
	}
	
}

/* End of file Assessment_year.php */
/* Location: ./application/modules/manage/controllers/Assessment_year.php */