<?php 

	/**
	 * Get name and department id of section by its ID section
	 * @param int $id
	 * @return string
	 */
	function get_section(int $id) : object
	{
		$CI =& get_instance();
		$CI->db->select('name, dept_id');
		$CI->db->from('sections');
		$CI->db->where('id', $id);
		return $CI->db->get()->row();
	}

	function get_section_by_nik(int $nik) : int
	{
		$CI =& get_instance();
		$CI->db->select('section_id');
		$CI->db->from('employes');
		$CI->db->where('nik', $nik);
		return $CI->db->get()->row()->section_id;
	}

	function get_section_by_jobtitle(int $id) : int
	{
		$CI =& get_instance();
		$CI->db->select('section');
		$CI->db->from('job_titles');
		$CI->db->where('id', $id);
		return $CI->db->get()->row()->section;
	}

	/**
	 * Get name of department by its ID department
	 * @param int $id
	 * @return string
	 */
	function get_department(int $id) : string
	{
		$CI =& get_instance();
		$CI->db->select('name');
		$CI->db->from('departements');
		$CI->db->where('id', $id);
		return $CI->db->get()->row()->name;
	}

	/**
	 * Get name of department by its section ID
	 * @param int $id
	 * @return object
	 */
	function get_department_by_section(int $id) : object
	{
		$CI =& get_instance();
		$CI->db->select('a.name, a.id');
		$CI->db->from('departements a');
		$CI->db->join('sections b', 'a.id = b.dept_id');
		$CI->db->where('b.id', $id);
		return $CI->db->get()->row();
	}

	/**
	 * Convert number to roman numeral
	 * @param int $number
	 * @return string
	 */
	function convert_to_roman(int $number) : string
	{
		switch ($number) {
			case 1:
				return 'I';
				break;

			case 2:
				return 'II';
				break;

			case 3:
				return 'III';
				break;

			case 4:
				return 'IV';
				break;

			case 5:
				return 'V';
				break;
			
			default:
				return 'VI';
				break;
		}
	}

	/**
	 * Check percentage of assessment form filling
	 * @param int $jobtitleId
	 * @return int
	 */
	function is_form_complete(int $jobtitleId, string $spv) : int
	{
		$CI =& get_instance();
		$activeYear = get_active_year();

		$totalForm 	= $CI->db->where('code', 'AF-'.$jobtitleId.'-'.$activeYear.'-'.$spv)
								->get('assessment_forms')->num_rows();

		$totalComplete 	= $CI->db->where('code', 'AF-'.$jobtitleId.'-'.$activeYear.'-'.$spv)
									->where('total_poin IS NOT NULL', NULL, FALSE)
									->get('assessment_forms')->num_rows();
		// create percentage
		$percentage = ($totalComplete/$totalForm)*100;

		if (is_nan($percentage)) {
			$percentage = 0;
		}

		return $percentage;
	}

	/**
	 * Get active assessment year
	 * 
	 * @return string
	 */
	function get_active_year() : string
	{
		$CI =& get_instance();
		return $CI->db->where('is_active', 1)->get('assessment_years')->row()->year;
	}
	
	/**
	 * Check for completeness of assessent from filling
	 * @param int $amount
	 * @param string $nik
	 * @param string $year
	 * @return bool
	 */
	function is_value_complete($amount, $nik, $year)
    {
    	$CI =& get_instance();
        $formId = $CI->db->where('nik', $nik)->like('code',$year,'both')->get('assessment_forms')->row();
        
        if (isset($formId)) {
        	$filledCompetency = $CI->db->query("SELECT COUNT(distinct b.id_dictionary) AS totalFilled 
	        									FROM assessment_form_questions a 
		        								JOIN skill_units b ON a.skill_unit_id  = b.id 
		        								WHERE a.form_id = $formId->id 
		        								AND a.poin IS NOT NULL")->row()->totalFilled;

	        if ($amount == $filledCompetency) {
	            return true;
	        }	
        }
        
        return false;
    }
	
    /**
     * Get name of user
     * @param string $nik
     * @return string
     */
    function user_name($nik="") : string
    {
    	if (empty($nik)) {
    		return '';
    	}

    	$CI =& get_instance();
        $userName = $CI->db->where('nik', $nik)->get('employes')->row()->name;
        return $userName;
    }

    /**
     * Get skill type name by its id
     * @param int $id
     * @return string
     */
    function get_skill_type_name(int $id) : string
	{
		$CI =& get_instance();
		$CI->db->where('id', $id);
		return $CI->db->get('skill_types', 1)->row()->name;
	}

	/**
	 * Get competency dictionary name
	 * @param int $dictionaryId
	 * @return string
	 */
	function get_dictionary_detail(int $dictionaryId) : object
	{
		$CI =& get_instance();
		$CI->db->where('id', $dictionaryId);
		return $CI->db->get('skill_dictionaries', 1)->row();
	}

	/**
	 * Get skill type base on dixtionary id
	 * @param int $dictionaeryId
	 * @return array
	 */
	function skill_type_by_dictionary(int $dictionaryId) : object
	{
		$CI =& get_instance();
		$CI->db->where('id', $dictionaryId);
		return $CI->db->get('skill_types')->row();
	}
	
	/**
	 * Change date format to yyyy-mm-dd
	 * @param string $date
	 * @param string $delimiter
	 * @return string
	 */
	
	function date_format_ymd(string $date, string $delimiter) : string
	{
		$source = explode($delimiter, $date);
		return $source[2].'-'.$source[1].'-'.$source[0];
	}

	/**
	 * Get detail department
	 * @param int $id
	 * @return object
	 */
	function department_detail(int $id) : object
	{
		$CI =& get_instance();
		$CI->db->where('id', $id);
		return $CI->db->get('departements')->row();
	}
	

	/**
	 * Get section detail by department id
	 * @param int $id
	 * @return object
	 */
	function section_by_department(int $id) : object
	{
		$CI =& get_instance();
		$CI->db->where('dept_id', $id);
		return $CI->db->get('sections')->row();
	}
	
	/**
	 * Get section id detail
	 * @param int $id
	 * @return object
	 */
	function section_detail(int $id) : object
	{
		$CI =& get_instance();
		$CI->db->where('id', $id);
		return $CI->db->get('sections')->row();
	}

	/**
	 * Get name of job title
	 * @param $id
	 * @return -
	 */
	function get_jobtitle_name($id)
	{
		error_reporting(0);
		$CI =& get_instance();
		$CI->db->where('id', $id);
		return $CI->db->get('job_titles')->row()->name;
	}

	/**
	 * Get grade for absolute poin in assessment
	 * @param int $poin
	 * @return string
	 */
	function get_assessment_grade($poin = null) : string
	{
		$CI =& get_instance();
		$CI->load->model('assessment_model','assessment');
		$getRange = $CI->assessment->get_poin_range();

		foreach ($getRange as $keys => $value) {
			if (($poin >= $value->bottom_limit) && ($poin <= $value->top_limit)) {
				return $value->grade;
			}
		}
		return '-';
	}

	/**
	 * Get progress filled status
	 * @param string $state
	 * @return string
	 */
	function get_filling_state($form_code)
	{
		$CI =& get_instance();
		$state = $CI->db->get_where('assessment_form_state',['code_form' => $form_code])->row()->state;

		if (!isset($state)) {
			return 'AM';
		}

		return $state;
	}

	function get_position_name($id)
	{
		$CI =& get_instance();
		return $CI->db->get_where('positions', ['id' => $id])->row()->name;
	}

	function debug($value, $is_exit=0)
	{
		echo "<pre>";
		print_r ($value);
		echo "</pre>";

		if ($is_exit != 0) {
			die();	
		}
	}
