<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Role extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		if (!$this->kauth->getInstance()->hasIdentity()) {
			redirect('login');
		}

		$this->USER_TYPE_ID = $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;
		$this->USER_TYPE = $this->kauth->getInstance()->getIdentity()->USER_TYPE;
	}
	public function index()
	{
		$this->load->view('role/index', $data);
	}
}
