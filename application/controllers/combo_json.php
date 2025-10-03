<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class combo_json extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			// redirect('login');
		}       
		
		/* GLOBAL VARIABLE */ 
		
		$this->ID = $this->kauth->getInstance()->getIdentity()->ID;   
		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;   
		$this->USER_LOGIN = $this->kauth->getInstance()->getIdentity()->USER_LOGIN;   
		$this->NRP = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;   
		$this->USER_NAMA = $this->kauth->getInstance()->getIdentity()->USER_NAMA;   
		$this->USER_TYPE_ID = $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID;  
		$this->USER_TYPE = $this->kauth->getInstance()->getIdentity()->USER_TYPE;  
		$this->UNIT_KERJA_ID = $this->kauth->getInstance()->getIdentity()->UNIT_KERJA_ID; 
		$this->UNIT_KERJA = $this->kauth->getInstance()->getIdentity()->UNIT_KERJA;         
	}	
	
	function combo_bulan() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "01";
		$arr_json[$i]['text']	= "Januari";
		$i++;
		$arr_json[$i]['id']		= "02";
		$arr_json[$i]['text']	= "Februari";
		$i++;
		$arr_json[$i]['id']		= "03";
		$arr_json[$i]['text']	= "Maret";
		$i++;
		$arr_json[$i]['id']		= "04";
		$arr_json[$i]['text']	= "April";
		$i++;
		$arr_json[$i]['id']		= "05";
		$arr_json[$i]['text']	= "Mei";
		$i++;
		$arr_json[$i]['id']		= "06";
		$arr_json[$i]['text']	= "Juni";
		$i++;
		$arr_json[$i]['id']		= "07";
		$arr_json[$i]['text']	= "Juli";
		$i++;
		$arr_json[$i]['id']		= "08";
		$arr_json[$i]['text']	= "Agustus";
		$i++;
		$arr_json[$i]['id']		= "09";
		$arr_json[$i]['text']	= "September";
		$i++;
		$arr_json[$i]['id']		= "10";
		$arr_json[$i]['text']	= "Oktober";
		$i++;
		$arr_json[$i]['id']		= "11";
		$arr_json[$i]['text']	= "November";
		$i++;
		$arr_json[$i]['id']		= "12";
		$arr_json[$i]['text']	= "Desember";

		echo json_encode($arr_json);
	}


	function combo_tahun() 
	{	
		$tahun = 2017;
		for ($i=0; $i<15;$i++) {
			$tahun += 1;
			$arr_json[$i]['id']		= $tahun;
			$arr_json[$i]['text']	= $tahun;
		}

		echo json_encode($arr_json);
	}


	function kualifikasi() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "SLTA";
		$arr_json[$i]['text']	= "SMA / SMK";
		$i++;
		$arr_json[$i]['id']		= "DIPLOMA";
		$arr_json[$i]['text']	= "D1 / D2 / D3";
		$i++;
		$arr_json[$i]['id']		= "STRATA";
		$arr_json[$i]['text']	= "S1";
		$i++;
		$arr_json[$i]['id']		= "STRATA_2";
		$arr_json[$i]['text']	= "S2";
		$i++;
		$arr_json[$i]['id']		= "STRATA_3";
		$arr_json[$i]['text']	= "S3";

		echo json_encode($arr_json);
	}

	function kelas_kereta() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "EKONOMI";
		$arr_json[$i]['text']	= "Ekonomi";
		$i++;
		$arr_json[$i]['id']		= "BISNIS";
		$arr_json[$i]['text']	= "Bisnis";
		$i++;
		$arr_json[$i]['id']		= "EKSEKUTIF";
		$arr_json[$i]['text']	= "Eksekutif";

		echo json_encode($arr_json);
	}

	function title_gender() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "TUAN";
		$arr_json[$i]['text']	= "Tuan";
		$i++;
		$arr_json[$i]['id']		= "NYONYA";
		$arr_json[$i]['text']	= "Nyonya";

		echo json_encode($arr_json);
	}

	function tipe_identitas() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "KTP";
		$arr_json[$i]['text']	= "KTP";
		$i++;
		$arr_json[$i]['id']		= "SIM";
		$arr_json[$i]['text']	= "SIM";
		$i++;
		$arr_json[$i]['id']		= "PASPOR";
		$arr_json[$i]['text']	= "Paspor";

		echo json_encode($arr_json);
	}

	function metode_pembayaran() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "BCA-05090999999";
		$arr_json[$i]['text']	= "BCA-05090999999";
		$i++;
		$arr_json[$i]['id']		= "BRI-88009900987";
		$arr_json[$i]['text']	= "BRI-88009900987";
		$i++;
		$arr_json[$i]['id']		= "GOPAY-085731534411";
		$arr_json[$i]['text']	= "GOPAY-085731534411";

		echo json_encode($arr_json);
	}

	function kategori_kpi() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "nilai_ekonomi";
		$arr_json[$i]['text']	= "Nilai Ekonomi & Sosial Untuk Indonesia";
		$i++;
		$arr_json[$i]['id']		= "Inovasi_model_bisnis";
		$arr_json[$i]['text']	= "Inovasi Model Bisnis";
		$i++;
		$arr_json[$i]['id']		= "kepemimpinan_teknologi";
		$arr_json[$i]['text']	= "Kepemimpinan Teknologi";
		$i++;
		$arr_json[$i]['id']		= "pengembangan_investasi";
		$arr_json[$i]['text']	= "Pengembangan Investasi";
		$i++;
		$arr_json[$i]['id']		= "pengembangan_talenta";
		$arr_json[$i]['text']	= "Pengembangan Talenta";

		echo json_encode($arr_json);
	}

	function status() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "0";
		$arr_json[$i]['text']	= "Tidak Posting";
		$i++;
		$arr_json[$i]['id']		= "1";
		$arr_json[$i]['text']	= "Posting";

		echo json_encode($arr_json);
	}


	function status_aktif() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "1";
		$arr_json[$i]['text']	= translate("Aktif","Active");
		$i++;
		$arr_json[$i]['id']		= "0";
		$arr_json[$i]['text']	= translate("Tidak Aktif","Inactive");

		echo json_encode($arr_json);
	}

	function jenis_manajemen() 
	{
		$i = 0;
		$arr_json[$i]['id']		= "PENGURUS";
		$arr_json[$i]['text']	= translate("Pengurus","Management");
		$i++;
		$arr_json[$i]['id']		= "PEMEGANG_SAHAM";
		$arr_json[$i]['text']	= translate("Pemegang Saham","Shareholder");

		echo json_encode($arr_json);
	}


	function kategori_fasilitas() 
	{
		$i = 0;

		$query = $this->db->query(" SELECT KATEGORI_FASILITAS_ID, NAMA FROM KATEGORI_FASILITAS ORDER BY NAMA ASC");
		foreach ($query->result_array() as $row) {
			$arr_json[$i]['id']			= strtoupper($row["kategori_fasilitas_id"]);
			$arr_json[$i]['text']		= strtoupper($row["nama"]);
			$i++;
		}
		echo json_encode($arr_json);
	}

	function terminal() 
	{
		$i = 0;
		
		$query = $this->db->query(" SELECT TERMINAL_ID, NAMA FROM TERMINAL ORDER BY NAMA ASC");
		foreach ($query->result_array() as $row) {
			$arr_json[$i]['id']			= strtoupper($row["terminal_id"]);
			$arr_json[$i]['text']		= strtoupper($row["nama"]);
			$i++;
		}
		echo json_encode($arr_json);
	}


	function combo_type_user() 
	{
		$i = 0;
		$this->load->model("Usertype");
		$user_type = new Usertype();
		$user_type->selectByParams(array());

		$i = 0;

		while($user_type->nextRow()){
		
			$arr_json[$i]['id']			= strtoupper($user_type->getField('USER_TYPE_ID'));
			$arr_json[$i]['text']		= ucwords($user_type->getField('NAMA'));
			$i++;
		
		}

		echo json_encode($arr_json);
	}



	function combo_triwulan() 
	{
		$i = 0;

		$arr_json[$i]['id']			= 1;
		$arr_json[$i]['text']		= I;
		$i++;
		$arr_json[$i]['id']			= 2;
		$arr_json[$i]['text']		= II;
		$i++;
		$arr_json[$i]['id']			= 3;
		$arr_json[$i]['text']		= III;
		$i++;
		$arr_json[$i]['id']			= 4;
		$arr_json[$i]['text']		= IV;
		$i++;
		
		echo json_encode($arr_json);
	}


	function provinsi() 
	{
		$i = 0;
		$query = $this->db->query("select provinsi_id, nama from provinsi order by nama asc");
		foreach ($query->result_array() as $row) {
			$arr_json[$i]['id']			= strtoupper($row["provinsi_id"]);
			$arr_json[$i]['text']		= strtoupper($row["nama"]);
			$i++;
		}
		echo json_encode($arr_json);
	}


	function kota() 
	{
		$i = 0;
		$reqProvinsiId = $this->input->get("reqProvinsiId");
		if($reqProvinsiId == ""){
			$query = $this->db->query(" select kota_id, nama from kota order by nama asc");
			foreach ($query->result_array() as $row) {
				$arr_json[$i]['id']			= strtoupper($row["kota_id"]);
				$arr_json[$i]['text']		= strtoupper($row["nama"]);
				$i++;
			}
			echo json_encode($arr_json);
		}
		else {
			$query = $this->db->query(" select kota_id, nama from kota where provinsi_id = '$reqProvinsiId' order by nama asc");
			foreach ($query->result_array() as $row) {
				$arr_json[$i]['id']			= strtoupper($row["kota_id"]);
				$arr_json[$i]['text']		= strtoupper($row["nama"]);
				$i++;
			}
			echo json_encode($arr_json);		
		}	
	}

	function kecamatan() 
	{
		$i = 0;
		$reqKotaId = $this->input->get("reqKotaId");
		if($reqKotaId == ""){
			$query = $this->db->query("select kecamatan_id, nama from kecamatan order by nama asc");
			foreach ($query->result_array() as $row) {
				$arr_json[$i]['id']			= strtoupper($row["kecamatan_id"]);
				$arr_json[$i]['text']		= strtoupper($row["nama"]);
				$i++;
			}
			echo json_encode($arr_json);
		}
		else {
			$query = $this->db->query(" select kecamatan_id, nama from kecamatan where kota_id = '$reqKotaId' order by nama asc");
			foreach ($query->result_array() as $row) {
				$arr_json[$i]['id']			= strtoupper($row["kecamatan_id"]);
				$arr_json[$i]['text']		= strtoupper($row["nama"]);
				$i++;
			}
			echo json_encode($arr_json);		
		}	
	}

	function kelurahan() 
	{
		$i = 0;
		$reqKecamatanId = $this->input->get("reqKecamatanId");
		if($reqKecamatanId == ""){
			$query = $this->db->query(" select kelurahan_id, nama, kodepos from kelurahan order by nama asc");
			foreach ($query->result_array() as $row) {
				$arr_json[$i]['id']			= strtoupper($row["kelurahan_id"]);
				$arr_json[$i]['text']		= strtoupper($row["nama"]);
				$arr_json[$i]['kodepos']		= strtoupper($row["kodepos"]);
				$i++;
			}
			echo json_encode($arr_json);
		} 
		else {

			$query = $this->db->query(" select kelurahan_id, nama, kodepos from kelurahan where kecamatan_id = '$reqKecamatanId' order by nama asc");
			foreach ($query->result_array() as $row) {
				$arr_json[$i]['id']			= strtoupper($row["kelurahan_id"]);
				$arr_json[$i]['text']		= strtoupper($row["nama"]);
				$arr_json[$i]['kodepos']		= strtoupper($row["kodepos"]);
				$i++;
			}
			echo json_encode($arr_json);		
		}	
	}

		
}
?>