<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends CI_Controller {

	function __construct() {
		parent::__construct();

		$this->load->library('kauth');

		$this->db->query("SET DATESTYLE TO PostgreSQL,European;"); 
		$this->ID			= $this->kauth->getInstance()->getIdentity()->ID;
		$this->USERNAME		= $this->kauth->getInstance()->getIdentity()->USERNAME;
		$this->NAMA			= $this->kauth->getInstance()->getIdentity()->NAMA;
		$this->EMAIL		= $this->kauth->getInstance()->getIdentity()->EMAIL;
		$this->FOTO			= $this->kauth->getInstance()->getIdentity()->FOTO;
		$this->STATUS		= $this->kauth->getInstance()->getIdentity()->STATUS;
		$this->LAST_LOGIN	= $this->kauth->getInstance()->getIdentity()->LAST_LOGIN;		
	}
	
	public function index()
	{
		$this->load->view('email/index');
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