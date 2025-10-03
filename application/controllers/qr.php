<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once("functions/image.func.php");
include_once("functions/string.func.php");

class Qr extends CI_Controller {

	function __construct() {
		parent::__construct();
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// redirect('login');
		}		
		
		$this->ID = $this->kauth->getInstance()->getIdentity()->ID;
		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;
		$this->USER_GROUP = $this->kauth->getInstance()->getIdentity()->USER_GROUP;
		$this->PERUSAHAAN_ID = $this->kauth->getInstance()->getIdentity()->PERUSAHAAN_ID;
		$this->KODE_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->KODE_PERUSAHAAN;
		$this->NAMA_PERUSAHAAN = $this->kauth->getInstance()->getIdentity()->NAMA_PERUSAHAAN;
		$this->CABANG_ID = $this->kauth->getInstance()->getIdentity()->CABANG_ID;
		$this->KODE_CABANG = $this->kauth->getInstance()->getIdentity()->KODE_CABANG;
		$this->NAMA_CABANG = $this->kauth->getInstance()->getIdentity()->NAMA_CABANG;
		$this->SATUAN_KERJA_ID = $this->kauth->getInstance()->getIdentity()->SATUAN_KERJA_ID;
		$this->KODE_SATUAN_KERJA = $this->kauth->getInstance()->getIdentity()->KODE_SATUAN_KERJA;
		$this->NAMA_SATUAN_KERJA = $this->kauth->getInstance()->getIdentity()->NAMA_SATUAN_KERJA;
		$this->PEGAWAI_ID = $this->kauth->getInstance()->getIdentity()->PEGAWAI_ID;
		$this->KODE_PEGAWAI = $this->kauth->getInstance()->getIdentity()->KODE_PEGAWAI;
		$this->NAMA_PEGAWAI = $this->kauth->getInstance()->getIdentity()->NAMA_PEGAWAI;
		$this->JABATAN = $this->kauth->getInstance()->getIdentity()->JABATAN;
		$this->MULTIROLE = $this->kauth->getInstance()->getIdentity()->MULTIROLE;

		$this->db->query("SET DATESTYLE TO PostgreSQL,European;"); 
		
	}

	public function index()
	{

		$pg = $this->uri->segment(3, "home");
		$reqParse1 = $this->uri->segment(4, "");
		$reqParse2 = $this->uri->segment(5, "");
		$reqParse3 = $this->uri->segment(6, "");
		$reqParse4 = $this->uri->segment(7, "");
		$reqParse5 = $this->uri->segment(5, "");

		$view = array(
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$data = array(
			'pg' => $pg,
			'reqParse1' => $reqParse1,
			'reqParse2'	=> $reqParse2,
			'reqParse3'	=> $reqParse3,
			'reqParse4'	=> $reqParse4,
			'reqParse5'	=> $reqParse5
		);

		$this->load->view('qr/qr', $data);
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
			'reqFolder' => $reqFolder,
			'reqFilename' => $reqFilename,
			'reqParse1' => urldecode($reqParse1),
			'reqParse2' => urldecode($reqParse2),
			'reqParse3' => urldecode($reqParse3),
			'reqParse4' => urldecode($reqParse4),
			'reqParse5' => urldecode($reqParse5)
		);
		if($reqFolder == "main")
			$this->session->set_userdata('currentUrl', $reqFilename);
				
		$this->load->view($reqFolder.'/'.$reqFilename, $data);
	}	
	
	
}

?>