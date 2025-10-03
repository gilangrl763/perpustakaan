<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class kas_json extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			redirect('login');
		}       
		
		/* GLOBAL VARIABLE */ 
		$this->ID = $this->kauth->getInstance()->getIdentity()->ID;   
		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;       
		$this->USER_TYPE_ID = $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;       
		$this->JENIS = $this->kauth->getInstance()->getIdentity()->JENIS;       
		$this->TANGGAL = $this->kauth->getInstance()->getIdentity()->TANGGAL;       
		$this->KETERANGAN = $this->kauth->getInstance()->getIdentity()->KETERANGAN;       
		$this->NILAI = $this->kauth->getInstance()->getIdentity()->NILAI;       
	}	
	
	function add() 
	{
		$this->load->model("Kas");
		$kas		= new Kas();

		$reqId						= $this->input->post("reqId");
		$reqMode					= $this->input->post("reqMode");
		$reqJenis					= $this->input->post("reqJenis");
		$reqTanggal					= $this->input->post("reqTanggal");
		$reqKeterangan				= $this->input->post("reqKeterangan");
		$reqNilai					= $this->input->post("reqNilai");
		

		$kas->setField("KAS_ID", $reqId);
		$kas->setField("JENIS", $reqJenis);
		$kas->setField("TANGGAL", $reqTanggal);
		$kas->setField("KETERANGAN", $reqKeterangan);
		$kas->setField("NILAI", $reqNilai);
		$kas->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$kas->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($reqMode == "insert"){
			if($kas->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}
		else{
			if($kas->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "GAGAL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("Kas");
		$kas	= new Kas();
		
		$reqId	= $this->input->post("reqId");

		$kas->setField("KAS_ID", $reqId);
		if($kas->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}
		
}
?>