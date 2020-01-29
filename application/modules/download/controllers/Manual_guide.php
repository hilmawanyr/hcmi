<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Manual_guide extends CI_Controller {

	public function index()
	{
		$filepath = FCPATH."/assets/manual/form_assessment_manual_guide.pdf";
		// EDIT: I added some permission/file checking.
		if (!file_exists($filepath)) {
		    throw new Exception("File $filepath does not exist");
		}
		if (!is_readable($filepath)) {
		    throw new Exception("File $filepath is not readable");
		}
		http_response_code(200);
		header('Content-Length: '.filesize($filepath));
		header("Content-Type: application/pdf");
		header('Content-Disposition: attachment; filename="form_assessment_manual_guide.pdf"'); // feel free to change the suggested filename
		readfile($filepath);
	}

}

/* End of file Manual_guide.php */
/* Location: ./application/modules/download/controllers/Manual_guide.php */