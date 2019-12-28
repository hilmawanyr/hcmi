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
	function is_form_complete(int $jobtitleId) : int
	{
		$CI =& get_instance();
		$activeYear = get_active_year();

		$totalForm 	= $CI->db->where('code', 'AF-'.$jobtitleId.'-'.$activeYear)
								->get('assessment_forms')->num_rows();

		$totalComplete 	= $CI->db->where('code', 'AF-'.$jobtitleId.'-'.$activeYear)
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
        $formId = $CI->db->where('nik', $nik)->like('code',$year,'before')->get('assessment_forms')->row();
        
        $filledCompetency = $CI->db->query("SELECT * from assessment_form_questions where form_id = '".$formId->id."' and poin IS NOT NULL")->result();

        if ($amount == count($filledCompetency)) {
            return true;
        }

        return false;
    }
	
    /**
     * Get name of user
     * @param string $nik
     * @return string
     */
    function user_name(string $nik) : string
    {
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
		$this->db->where('id', $dictionaryId);
		return $this->db->get('skill_types')->row();
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