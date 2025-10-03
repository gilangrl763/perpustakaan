<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class penduduk_json extends CI_Controller {

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
		$this->load->model("Penduduk");
		$penduduk		= new Penduduk();

		$reqId						= $this->input->post("reqId");
		$reqMode					= $this->input->post("reqMode");
		$reqKartuKeluargaId			= $this->input->post("reqKartuKeluargaId");
		$reqNama					= $this->input->post("reqNama");
		$reqNIK						= $this->input->post("reqNIK");
		$reqJenisKelamin   	 		= $this->input->post("reqJenisKelamin");
		$reqTempatLahir				= $this->input->post("reqTempatLahir");
		$reqTanggalLahir			= $this->input->post("reqTanggalLahir");
		$reqAgama					= $this->input->post("reqAgama");
		$reqPendidikan				= $this->input->post("reqPendidikan");
		$reqStatusPerkawinan		= $this->input->post("reqStatusPerkawinan");
		$reqTanggalPerkawinan		= $this->input->post("reqTanggalPerkawinan");
		$reqHubunganKeluarga		= $this->input->post("reqHubunganKeluarga");
		$reqKewarganegaraan			= $this->input->post("reqKewarganegaraan");
		$reqNoPaspor				= $this->input->post("reqNoPaspor");
		$reqNoKitap					= $this->input->post("reqNoKitap");
		$reqAyah					= $this->input->post("reqAyah");

		$penduduk->setField("PENDUDUK_ID", $reqId);
		$penduduk->setField("KARTU_KELUARGA_ID", $reqKartuKeluargaId);
		$penduduk->setField("NAMA", $reqNama);
		$penduduk->setField("NIK", $reqNIK);
		$penduduk->setField("JENIS_KELAMIN", $reqJenisKelamin);
		$penduduk->setField("TEMPAT_LAHIR", $reqTempatLahir);
		$penduduk->setField("TANGGAL_LAHIR", $reqTanggalLahir);
		$penduduk->setField("AGAMA", $reqAgama);
		$penduduk->setField("PENDIDIKAN", $reqPendidikan);
		$penduduk->setField("STATUS_PERKAWINAN", $reqStatusPerkawinan);
		$penduduk->setField("TANGGAL_PERKAWINAN", $reqTanggalPerkawinan);
		$penduduk->setField("HUBUNGAN_KELUARGA", $reqHubunganKeluarga);
		$penduduk->setField("KEWARGANEGARAAN", $reqKewarganegaraan);
		$penduduk->setField("NO_PASPOR", $reqNoPaspor);
		$penduduk->setField("NO_KITAP", $reqNoKitap);
		$penduduk->setField("AYAH", $reqAyah);
		$penduduk->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$penduduk->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		
		if($reqMode == "insert"){
			if($penduduk->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "BERHASIL|Data gagal disimpan";
			}
		}
		else{
			if($penduduk->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "BERHASIL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("Penduduk");
		$penduduk	= new Penduduk();
		
		$reqId	= $this->input->post("reqId");

		$penduduk->setField("PENDUDUK_ID", $reqId);
		if($penduduk->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}
		
}
?>