<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class kartu_keluarga_json extends CI_Controller {

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
		$this->NOMOR = $this->kauth->getInstance()->getIdentity()->NOMOR;       
		$this->ALAMAT = $this->kauth->getInstance()->getIdentity()->ALAMAT;       
		$this->RT = $this->kauth->getInstance()->getIdentity()->RT;       
		$this->RW = $this->kauth->getInstance()->getIdentity()->RW;       
		$this->KELURAHAN = $this->kauth->getInstance()->getIdentity()->KELURAHAN;       
		$this->KECAMATAN = $this->kauth->getInstance()->getIdentity()->KECAMATAN;       
		$this->KOTA = $this->kauth->getInstance()->getIdentity()->KOTA;       
		$this->PROVINSI = $this->kauth->getInstance()->getIdentity()->PROVINSI;       
		$this->KODE_POS = $this->kauth->getInstance()->getIdentity()->KODE_POS;       
	}	
	
	function add() 
	{
		$this->load->model("KartuKeluarga");
		$kartu_keluarga	= new KartuKeluarga();
		
		$reqId			= $this->input->post("reqId");
		$reqMode		= $this->input->post("reqMode");
		$reqNomor		= $this->input->post("reqNomor");
		$reqAlamat		= $this->input->post("reqAlamat");
		$reqRT			= $this->input->post("reqRT");
		$reqRW			= $this->input->post("reqRW");
		$reqKelurahan	= $this->input->post("reqKelurahan");
		$reqKecamatan	= $this->input->post("reqKecamatan");
		$reqKota		= $this->input->post("reqKota");
		$reqProvinsi	= $this->input->post("reqProvinsi");
		$reqKodePos		= $this->input->post("reqKodePos");

		$kartu_keluarga->setField("KARTU_KELUARGA_ID", $reqId);
		$kartu_keluarga->setField("NOMOR", $reqNomor);
		$kartu_keluarga->setField("ALAMAT", $reqAlamat);
		$kartu_keluarga->setField("RT", $reqRT);
		$kartu_keluarga->setField("RW", $reqRW);
		$kartu_keluarga->setField("KELURAHAN", $reqKelurahan);
		$kartu_keluarga->setField("KECAMATAN", $reqKecamatan);
		$kartu_keluarga->setField("KOTA", $reqKota);
		$kartu_keluarga->setField("PROVINSI", $reqProvinsi);
		$kartu_keluarga->setField("KODE_POS", $reqKodePos);
		$kartu_keluarga->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$kartu_keluarga->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		
		if($reqMode == "insert"){
			if($kartu_keluarga->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "BERHASIL|Data gagal disimpan";
			}
		}
		else{
			if($kartu_keluarga->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "BERHASIL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("KartuKeluarga");
		$kartu_keluarga	= new KartuKeluarga();
		
		$reqId	= $this->input->post("reqId");

		$kartu_keluarga->setField("KARTU_KELUARGA_ID", $reqId);
		if($kartu_keluarga->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}
		
}
?>