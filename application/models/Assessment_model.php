<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assessment_model extends CI_Model {

	/**
	 * Get list of all jobtitle by its group id
	 * @param int $group
	 * @return array
	 */
	public function jobtitle_by_grade(int $grade) : array
	{
		$this->db->select('
			a.job_title_id, 
			a.section_id,
			a.grade,
			b.name as jobtitleName,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('sections c', 'c.id = a.section_id');
		$this->db->where('a.grade <=', $grade);
		$this->db->group_by('a.job_title_id, a.grade, a.section_id');
		$this->db->order_by('a.grade', 'asc');
		return $this->db->get()->result();
	}

	/**
	 * Get list of all jobtitle by group id and section id
	 * @param int $group
	 * @param int $section
	 * @return array
	 */
	public function jobtitle_by_grade_and_section(int $grade, int $section) : array
	{
		$this->db->select('
			a.job_title_id, 
			a.section_id,
			a.grade,
			b.name as jobtitleName,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('sections c', 'c.id = a.section_id');
		$this->db->where('a.grade <=', $grade);
		$this->db->where('a.section_id', $section);
		$this->db->group_by('a.job_title_id, a.grade');
		$this->db->order_by('a.grade, b.name', 'asc');
		return $this->db->get()->result();
	}

	/**
	 * Get list of all jobtitle by group id and section id
	 * @param int $group
	 * @param int $section
	 * @return array
	 */
	public function jobtitle_by_grade_and_department(int $grade, int $department) : array
	{
		$this->db->select('
			a.job_title_id, 
			a.dept_id,
			a.grade,
			b.name as jobtitleName,
			b.section as section_id,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('departements c', 'c.id = a.dept_id');
		$this->db->where('a.grade <=', $grade);
		$this->db->where('a.dept_id', $department);
		$this->db->group_by('a.job_title_id, a.grade');
		$this->db->order_by('a.grade, b.name', 'asc');
		return $this->db->get()->result();
	}

	/**
	 * Get list of all jobtitle by group id and section id
	 * @param int $group
	 * @param int $section
	 * @return array
	 */
	public function jobtitle_by_director(int $grade) : array
	{
		$this->db->select('
			a.job_title_id, 
			a.dept_id,
			a.grade,
			b.name as jobtitleName,
			b.section as section_id,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('departements c', 'c.id = a.dept_id');
		$this->db->where('a.grade <=', $grade);
		$this->db->group_by('a.job_title_id, a.dept_id, a.grade');
		$this->db->order_by('a.grade, b.name', 'asc');
		return $this->db->get()->result();
	}
	
	/**
	 * Get competency dictionary for each job title
	 * @param int $jobtitle
	 * @return array
	 */
	public function get_competency(int $jobtitle) : object
	{
		$this->db->select('
			a.job_id,
			a.skill_id,
			a.level,
			b.id,
			b.name_id,
			b.description');
		$this->db->from('skill_matrix a');
		$this->db->join('skill_dictionaries b', 'a.skill_id = b.id');
		$this->db->where('a.job_id', $jobtitle);
		$this->db->where('a.deleted_at');
		return $this->db->get();
	}

	/**
	 * Check whether assessment form is exist
	 * @param string $activeYear
	 * @param int $jobtitle
	 * @return array
	 */
	public function is_assessment_form_exist(string $activeYear, int $jobtitle) : object
	{
		$this->db->where('job_id', $jobtitle);
		$this->db->like('code', $activeYear, 'before');
		return $this->db->get('assessment_forms');
	}

	/**
	 * Get competency for each employes by their job title
	 * @param string $activeYear
	 * @param int $jobtitle
	 * @return array
	 */
	public function competency_by_jobtitle(string $activeYear, int $jobtitle) : object
	{
		$this->db->select('*');
		$this->db->from('employes a');
		$this->db->join('assessment_forms b', 'a.nik = b.nik', 'left');
		$this->db->where('a.job_title_id', $jobtitle);
		$this->db->like('b.code', $activeYear, 'before');
		$this->db->order_by('b.total_poin ASC, a.name ASC');
		return $this->db->get();
	}

	/**
	 * Get number of filled assessment
	 * @param int $jobtitle
	 * @return int
	 */
	public function complete_assessment(int $jobtitle) : int
	{
		$this->db->distinct();
		$this->db->select('c.id_dictionary, a.form_id');
		$this->db->from('assessment_form_questions a');
		$this->db->join('assessment_forms b', 'a.form_id = b.id');
		$this->db->join('skill_units c', 'a.skill_unit_id = c.id');
		$this->db->where('b.job_id', $jobtitle);
		$this->db->where('a.poin is not null', NULL, FALSE);
		$this->db->group_by('c.id_dictionary, a.form_id');
		return $this->db->get()->num_rows();

		/*$this->db->select('a.code');
		$this->db->from('assessment_forms a');
		$this->db->join('assessment_form_questions b', 'a.id = b.form_id');
		$this->db->where('b.poin IS NOT NULL', NULL, FALSE);
		$this->db->where('a.job_id', $jobtitle);
		return $this->db->get()->num_rows();*/
	}

	/**
	 * Check is employe has assessment form
	 * @param int $jobtitle
	 * @param string $activeYear
	 * @param string $nik
	 * @return int
	 */
	public function is_employe_has_form(int $jobtitle, string $activeYear, string $nik) : int
	{
		// assessment form code
		$code = 'AF-'.$jobtitle.'-'.$activeYear;
		$this->db->where('code', $code);
		$this->db->where('nik', $nik);
		$this->db->where('job_id', $jobtitle);
		return $this->db->get('assessment_forms')->num_rows();
	}
	
	/**
	 * Create question competency
	 * @param int $jobtitle
	 * @return array
	 */
	public function create_assessment_question(int $jobtitle) : array
	{
		$this->db->select('
				job_titles.id as job_title,
                job_titles.group,
                skill_units.level AS lv_unit,
                skill_matrix.level AS lv_matrix,
                skill_matrix.skill_id,
                skill_units.id as unit_id,
                skill_units.description,
                (SELECT weight FROM assessment_form_weight 
                WHERE job_group_code = "G99"
                AND level = skill_matrix.level 
                AND unit = skill_units.level) as bobot'
			);
		$this->db->from('job_titles');
		$this->db->join('skill_matrix', 'skill_matrix.job_id = job_titles.id');
		$this->db->join('skill_units', 'skill_units.id_dictionary = skill_matrix.skill_id');
		$this->db->where('skill_units.deleted_at');
		$this->db->where('skill_matrix.deleted_at');
		$this->db->where('job_titles.id', $jobtitle);
		return $this->db->get()->result();
	}

	/**
	 * Get competency base on nik, job title, and active assessment year
	 * @param string $nik
	 * @param int $jobtitle 
	 * @param int $skillId
	 * @param string $activeYear
	 * @return array
	 */
	public function get_competency_for_assessment(string $nik, int $jobtitle, int $skillId, string $activeYear) : array
	{
		$competency = $this->db->query("SELECT 
        									assessment_forms.*,
				                            assessment_form_questions.skill_unit_id AS unit_question,
				                            assessment_form_questions.weight,
				                            assessment_form_questions.poin, 
				                            skill_units.description
										FROM assessment_forms
										JOIN assessment_form_questions ON assessment_forms.id = assessment_form_questions.form_id
										JOIN skill_units ON skill_units.id = assessment_form_questions.skill_unit_id
										WHERE assessment_forms.job_id = '$jobtitle'
										AND assessment_forms.nik = '$nik'
										AND skill_units.id_dictionary = '$skillId'
										AND assessment_forms.code like '%-$activeYear'
										AND skill_units.deleted_at IS NULL")->result();
		return $competency;
	}

	/**
	 * Get assessment matrix depend on job title
	 * @param int $jobtitleId
	 * @return array
	 */
	public function get_assessment_matrix(int $jobtitleId) : array
	{
		$matrix = $this->db->query("SELECT 
										skill_matrix.job_id,
										skill_matrix.skill_id,
										skill_matrix.level,
										skill_dictionaries.name_id,
										skill_dictionaries.description 
									FROM skill_matrix 
									JOIN skill_dictionaries ON skill_matrix.skill_id = skill_dictionaries.id 
									WHERE skill_matrix.job_id = '$jobtitleId' 
									AND skill_matrix.deleted_at IS NULL")->result();
		return $matrix;
	}

	/**
	 * Get each poin of each employe assessment
	 * @param int $skillId
	 * @param string $nik
	 * @return array
	 */
	public function get_poin(int $skillId, string $nik) : array
	{
		$activeYear = get_active_year();
		
        $poin 	= $this->db->query("SELECT 
	        							skill_units.description, 
	        							assessment_form_questions.poin, 
	        							assessment_form_questions.weight
	        						FROM skill_units
	        						JOIN assessment_form_questions ON skill_units.id = assessment_form_questions.skill_unit_id
									WHERE skill_units.id_dictionary = '$skillId'
									AND assessment_form_questions.form_id = 
										(SELECT id from assessment_forms where nik = '$nik' and code like '%$activeYear')
									AND assessment_form_questions.poin IS NOT NULL ")->result();
        return $poin;
	}

	/**
	 * Get poin range
	 * 
	 * @return array
	 */
	public function get_poin_range() : array
	{
		return $this->db->get('poin_grades')->result();		
	}

	public function get_jobtitle_by_head(int $nik=0) : array 
	{
		$this->db->select('
			a.job_title_id, 
			a.section_id,
			a.grade,
			emr.head,
			b.name as jobtitleName,
			c.name as sectionName,
			count(a.id) as numberOfPeople');
		$this->db->from('employes a');
		$this->db->join('employee_relations emr', 'emr.nik = a.nik');
		$this->db->join('job_titles b', 'a.job_title_id = b.id');
		$this->db->join('sections c', 'c.id = a.section_id');
		$this->db->where_in('emr.head', $this->get_head($nik));
		$this->db->group_by('a.job_title_id, a.section_id, a.grade, emr.head');
		$this->db->order_by('a.grade', 'asc');
		return $this->db->get()->result();
	}

	public function get_partisipant(int $nik=0, int $job_id=0) : array 
	{
		$this->db->select('a.*');
		$this->db->from('employes a');
		$this->db->join('employee_relations emr', 'emr.nik = a.nik');
		$this->db->where_in('emr.head', $this->get_head($nik));
		$this->db->where('a.job_title_id', $job_id);
		$this->db->order_by('a.name', 'asc');
		return $this->db->get()->result();
	}

	public function get_first_state(int $nik=0, int $job_id=0){
		
		$head = $this->get_head($nik);
		$head = implode( ", ", $head);

		$query = $this->db->query("SELECT `emr`.`head`, (SELECT `positions`.`code` FROM `employes` JOIN `positions` ON `employes`.`position_id` = `positions`.`id` WHERE `nik` = `emr`.`head`) as code FROM `employes` `a` JOIN `employee_relations` `emr` ON `emr`.`nik` = `a`.`nik` WHERE `emr`.`head` IN($head) AND `a`.`job_title_id` = $job_id ORDER BY `a`.`name` ASC LIMIT 1")->row();

		return $query->code;
	}

	public function get_head(int $nik=0) : array 
	{
		$head        = array($nik);
		$filter      = array();
		$filter_temp = array();

		$team 	= $this->db->query("SELECT emr.nik FROM employee_relations emr
									JOIN employes em ON emr.nik = em.nik  
									WHERE emr.head = ".$nik)->result();

		foreach ($team as $key => $value) {
			array_push($filter,$value->nik);
		}

		// 7 patokan kira2
		for ($i=0; $i < 5; $i++) {	
			$y = count($filter);
			for ($i=0; $i < $y ; $i++) { 
				
				$team 	= $this->db->query("SELECT emr.nik FROM employee_relations emr
											JOIN employes em ON emr.nik = em.nik 
											WHERE emr.head = ".$filter[$i])->result();

				if (count($team) > 0) {
					foreach ($team as $key => $value) {
						array_push($filter_temp,$value->nik);
					}
					array_push($head, $filter[$i]);
				}
			}
			
			if (count($filter_temp) == 0) {
				break;
			}

			$filter = $filter_temp;
			$filter_temp = array();
		}

		return $head;
	}

	public function get_continue_state(int $nik=0) : string {
		$query = $this->db->query("SELECT er.head, (SELECT p.code FROM employes e JOIN positions p ON e.position_id = p.id WHERE e.nik = er.head) as code 
								FROM employee_relations er 
								JOIN employes em ON er.nik = em.nik 
								WHERE er.nik = $nik")->row();
		return $query->code;
	}

}

/* End of file Assessment.php */
/* Location: ./application/models/Assessment.php */