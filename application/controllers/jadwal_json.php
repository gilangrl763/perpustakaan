<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class jadwal_json extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			redirect('login');
		}       
		
		/* GLOBAL VARIABLE */ 
		$this->ID 						= $this->kauth->getInstance()->getIdentity()->ID;       
		$this->KODE 					= $this->kauth->getInstance()->getIdentity()->KODE;       
		$this->KERETA_ID 				= $this->kauth->getInstance()->getIdentity()->KERETA_ID;           
		$this->STASIUN_ID_KEBERANGKATAN = $this->kauth->getInstance()->getIdentity()->STASIUN_ID_KEBERANGKATAN;           
		$this->TANGGAL_KEBERANGKATAN 	= $this->kauth->getInstance()->getIdentity()->TANGGAL_KEBERANGKATAN;           
		$this->JAM_KEBERANGKATAN		= $this->kauth->getInstance()->getIdentity()->JAM_KEBERANGKATAN;           
		$this->STASIUN_ID_KEDATANGAN 	= $this->kauth->getInstance()->getIdentity()->STASIUN_ID_KEDATANGAN;           
		$this->TANGGAL_KEDATANGAN 		= $this->kauth->getInstance()->getIdentity()->TANGGAL_KEDATANGAN;           
		$this->JAM_KEDATANGAN 			= $this->kauth->getInstance()->getIdentity()->JAM_KEDATANGAN;           
		$this->KETERANGAN 				= $this->kauth->getInstance()->getIdentity()->KETERANGAN;           
	}	
	
	function add() 
	{
		$this->load->model("Jadwal");
		$jadwal		= new Jadwal();

		$reqId						= $this->input->post("reqId");
		$reqMode					= $this->input->post("reqMode");
		$reqKode					= $this->input->post("reqKode");
		$reqKeretaId				= $this->input->post("reqKeretaId");
		$reqStasiunIdKeberangkatan	= $this->input->post("reqStasiunIdKeberangkatan");
		$reqTanggalKeberangkatan	= $this->input->post("reqTanggalKeberangkatan");
		$reqJamKeberangkatan		= $this->input->post("reqJamKeberangkatan");
		$reqStasiunIdKedatangan		= $this->input->post("reqStasiunIdKedatangan");
		$reqTanggalKedatangan		= $this->input->post("reqTanggalKedatangan");
		$reqJamKedatangan			= $this->input->post("reqJamKedatangan");
		$reqKeterangan				= $this->input->post("reqKeterangan");
		$reqKuota					= $this->input->post("reqKuota");
		$reqHarga					= $this->input->post("reqHarga");
		$reqKelas					= $this->input->post("reqKelas");

		//HITUNG DURASI
		$waktu_awal  = strtotime($reqTanggalKeberangkatan." ".$reqJamKeberangkatan.":00");
        $waktu_akhir = strtotime($reqTanggalKedatangan." ".$reqJamKedatangan.":00");
        
        //menghitung selisih dengan hasil detik
        $diff = $waktu_akhir - $waktu_awal;
        
        //membagi detik menjadi jam
        $jam = floor($diff / (60 * 60));
        
        //membagi sisa detik setelah dikurangi $jam menjadi menit
        $menit = floor(($diff - $jam * (60 * 60))/60);

		$durasi = $jam." Jam ".$menit." Menit";
		//END HITUNG DURASI
		
		$jadwal->setField("JADWAL_ID", $reqId);
		$jadwal->setField("KODE", $reqKode);
		$jadwal->setField("KERETA_ID", $reqKeretaId);
		$jadwal->setField("STASIUN_ID_KEBERANGKATAN", $reqStasiunIdKeberangkatan);
		$jadwal->setField("TANGGAL_KEBERANGKATAN", $reqTanggalKeberangkatan);
		$jadwal->setField("JAM_KEBERANGKATAN", $reqJamKeberangkatan);
		$jadwal->setField("STASIUN_ID_KEDATANGAN", $reqStasiunIdKedatangan);
		$jadwal->setField("TANGGAL_KEDATANGAN", $reqTanggalKedatangan);
		$jadwal->setField("JAM_KEDATANGAN", $reqJamKedatangan);
		$jadwal->setField("KETERANGAN", $reqKeterangan);
		$jadwal->setField("DURASI", $durasi);
		$jadwal->setField("KUOTA", $reqKuota);
		$jadwal->setField("HARGA", $reqHarga);
		$jadwal->setField("HARGA", $reqKelas);
		$jadwal->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$jadwal->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($reqMode == "insert"){
			if($jadwal->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}
		else{
			if($jadwal->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "GAGAL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("Jadwal");
		$jadwal	= new Jadwal();
		
		$reqId	= $this->input->post("reqId");

		$jadwal->setField("JADWAL_ID", $reqId);
		if($jadwal->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}
		
}
?>