<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Model {

	public $variable;

	public function __construct()
	{
		parent::__construct();
		
	}

	/**
	 * Check whether user with the $nik is exist
	 * @param string $nik
	 * @return object stdClass
	 */
	public function is_user_exist(string $ldap_uid) : ?object
	{
		return $this->db->select('nik, password')->from('users')->where('ldap_uid', $ldap_uid)->get()->row();
	}

	/**
	 * Get user data
	 * @param string $nik
	 * @return array
	 */
	public function get_user(string $nik) : object
	{
		$this->db->select('
			a.nik, 
			a.name, 
			a.section_id,
			a.grade, 
			a.position_id, 
			a.job_title_id, 
			b.group_id, 
			b.level,
			d.id');
		$this->db->from('employes a');
		$this->db->join('users b', 'a.nik = b.nik');
		$this->db->join('sections c', 'a.section_id = c.id', 'left');
		$this->db->join('departements d', 'd.id = a.dept_id', 'left');
		$this->db->where('a.nik', $nik);

		return $this->db->get()->row();
	}

	public function get_position_grade($position_id) : int
	{
		$grade = $this->db->get_where('positions', ['id' => $position_id])->row()->grade;
		return $grade;
	}
	
	/**
	 * Change password
	 * @param string $nik
	 * @param string $pass
	 * @return void
	 */
	public function update_password(string $nik,string $pass) : void
	{
		$this->db->where('nik', $nik);
		$this->db->update('users', ['password' => $pass]);
		return; 
	}

	/**
	 * Get auth log
	 * @param int $nik; default NULL
	 * @return array
	 */
	public function get_auth_log(int $nik = NULL) : array
	{
		switch ($nik) {
			case NULL:
				return $this->db->order_by('nik', 'asc')->get('log_auth')->result();
				break;
			
			default:
				return $this->db->where('nik', $nik)->order_by('nik', 'asc')->get('log_auth')->result();
				break;
		}
	}
}

/* End of file Login.php */
/* Location: ./application/models/Login.php */