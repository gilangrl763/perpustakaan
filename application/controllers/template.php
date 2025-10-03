<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends CI_Controller {

	function __construct() {
		parent::__construct();


		$this->db->query("SET DATESTYLE TO PostgreSQL,European;");  
				
		$this->load->library('kauth');     
	}
	
	public function index()
	{
		$this->load->view('registrasi/index');
	}
	
	public function loadUrl()
	{
		
		$reqFolder = $this->uri->segment(3, "");
		$reqFilename = $this->uri->segment(4, "");
		$reqParse1 = $this->uri->segment(5, "");
		$reqParse2 = $this->uri->segment(6, "");
		$reqParse3 = $this->uri->segment(7, "");
		$reqParse4 = $this->uri->segment(8, "");
		$reqParse5 = $this->uri->segment(9, "");
		$data = array(
			'reqParse1' => $reqParse1,
			'reqParse2' => $reqParse2,
			'reqParse3' => $reqParse3,
			'reqParse4' => $reqParse4,
			'reqParse5' => urldecode($reqParse5)
		);
		$this->load->view($reqFolder.'/'.$reqFilename, $data);
	}	
}