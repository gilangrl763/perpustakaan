<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class stasiun_json extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// redirect('login');
		}       
		
		/* GLOBAL VARIABLE */ 
		$this->ID 				= $this->kauth->getInstance()->getIdentity()->ID;   
		$this->USER_LOGIN_ID 	= $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;       
		$this->USER_TYPE_ID 	= $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;       
		$this->KODE 			= $this->kauth->getInstance()->getIdentity()->KODE;       
		$this->NAMA 			= $this->kauth->getInstance()->getIdentity()->NAMA;           
		$this->ALAMAT 			= $this->kauth->getInstance()->getIdentity()->ALAMAT;           
		$this->KELURAHAN_ID 	= $this->kauth->getInstance()->getIdentity()->KELURAHAN_ID;           
		$this->KECAMATAN_ID		= $this->kauth->getInstance()->getIdentity()->KECAMATAN_ID;           
		$this->KOTA_ID 			= $this->kauth->getInstance()->getIdentity()->KOTA_ID;           
		$this->PROVINSI_ID 		= $this->kauth->getInstance()->getIdentity()->PROVINSI_ID;           
		$this->TELEPON 			= $this->kauth->getInstance()->getIdentity()->TELEPON;           
		$this->FAX 				= $this->kauth->getInstance()->getIdentity()->FAX;           
	}	
	
	function add() 
	{
		$this->load->model("Stasiun");
		$stasiun		= new Stasiun();

		$reqId						= $this->input->post("reqId");
		$reqMode					= $this->input->post("reqMode");
		$reqKode					= $this->input->post("reqKode");
		$reqNama					= $this->input->post("reqNama");
		$reqAlamat					= $this->input->post("reqAlamat");
		$reqKelurahanId				= $this->input->post("reqKelurahanId");
		$reqKecamatanId				= $this->input->post("reqKecamatanId");
		$reqKotaId					= $this->input->post("reqKotaId");
		$reqProvinsiId				= $this->input->post("reqProvinsiId");
		$reqTelepon					= $this->input->post("reqTelepon");
		$reqFax						= $this->input->post("reqFax");
		

		$stasiun->setField("STASIUN_ID", $reqId);
		$stasiun->setField("KODE", $reqKode);
		$stasiun->setField("NAMA", $reqNama);
		$stasiun->setField("ALAMAT", $reqAlamat);
		$stasiun->setField("KELURAHAN_ID", $reqKelurahanId);
		$stasiun->setField("KECAMATAN_ID", $reqKecamatanId);
		$stasiun->setField("KOTA_ID", $reqKotaId);
		$stasiun->setField("PROVINSI_ID", $reqProvinsiId);
		$stasiun->setField("TELEPON", $reqTelepon);
		$stasiun->setField("FAX", $reqFax);
		$stasiun->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$stasiun->setField("UPDATED_BY", $this->USER_LOGIN_ID);


		if($reqMode == "insert"){
			if($stasiun->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}
		else{
			if($stasiun->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "GAGAL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("Stasiun");
		$stasiun	= new Stasiun();
		
		$reqId	= $this->input->post("reqId");

		$stasiun->setField("STASIUN_ID", $reqId);
		if($stasiun->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}

	function combo() 
	{
		$this->load->model("Stasiun");
		$stasiun	= new Stasiun();

		$i = 0;
		$arr_json = array();
		$stasiun->selectByParams(array());
		while($stasiun->nextRow())
		{
			$arr_json[$i]['id']		= $stasiun->getField("STASIUN_ID");
			$arr_json[$i]['text']	= $stasiun->getField("NAMA");
			$arr_json[$i]['KODE']	= $stasiun->getField("KODE");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
		
}
?>