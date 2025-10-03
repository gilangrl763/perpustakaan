<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class pemesanan_json extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			redirect('login');
		}       
		
		/* GLOBAL VARIABLE */ 
		$this->ID				= $this->kauth->getInstance()->getIdentity()->ID;   
		$this->USER_LOGIN_ID	= $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;   
		$this->USER_TYPE_ID		= $this->kauth->getInstance()->getIdentity()->USER_TYPE_ID; 
		$this->NAMA				= $this->kauth->getInstance()->getIdentity()->NAMA;   
		$this->EMAIL			= $this->kauth->getInstance()->getIdentity()->EMAIL;   
		$this->STATUS			= $this->kauth->getInstance()->getIdentity()->STATUS;     
	}	
	
	function add() 
	{
		$this->load->model("Pemesanan");
		$pemesanan = new Pemesanan();

		$reqId						= $this->input->post("reqId");
		$reqMode					= $this->input->post("reqMode");
		$reqKode					= $this->input->post("reqKode");
		$reqUserLoginId				= $this->input->post("reqUserLoginId");
		$reqJadwalId				= $this->input->post("reqJadwalId");
		$reqJumlahPenumpang			= $this->input->post("reqJumlahPenumpang");
		$reqHarga					= $this->input->post("reqHarga");
		$reqMetodePembayaran		= $this->input->post("reqMetodePembayaran");
		$reqStatus					= $this->input->post("reqStatus");
		$reqKeterangan				= $this->input->post("reqKeterangan");

		$reqTitleGender				= $this->input->post("reqTitleGender");
		$reqTipeIdentitas			= $this->input->post("reqTipeIdentitas");
		$reqNamaPenumpang			= $this->input->post("reqNamaPenumpang");
		$reqNomerIdentitas			= $this->input->post("reqNomerIdentitas");

		//VALIDASI
		if($reqJadwalId == ""){
			echo "GAGAL|Jadwal belum ditentukan";
			return;
		}
		elseif($reqJumlahPenumpang == ""){
			echo "GAGAL|Jadwal belum ditentukan";
			return;
		}
		//END VALIDASI

		if($reqStatus == ""){
			$reqStatus = "POSTING";
		}

		if($reqKode == ""){
			function randomKode() {
			    $kata = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
			    $kode = array();
			    $panjangKata = strlen($kata) - 1;
			    for ($i = 0; $i < 8; $i++) {
			        $n = rand(0, $panjangKata);
			        $kode[] = $kata[$n];
			    }

			    return implode($kode);
			}

			$reqKode 	= randomKode();
		}

		$pemesanan->setField("PEMESANAN_ID", $reqId);
		$pemesanan->setField("KODE", $reqKode);
		$pemesanan->setField("USER_LOGIN_ID", $reqUserLoginId);
		$pemesanan->setField("JADWAL_ID", $reqJadwalId);
		$pemesanan->setField("JUMLAH_PENUMPANG", $reqJumlahPenumpang);
		$pemesanan->setField("HARGA", $reqHarga);
		$pemesanan->setField("METODE_PEMBAYARAN", $reqMetodePembayaran);
		$pemesanan->setField("STATUS", $reqStatus);
		$pemesanan->setField("KETERANGAN", setQuote($reqKeterangan));
		$pemesanan->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$pemesanan->setField("UPDATED_BY", $this->USER_LOGIN_ID);

		if($reqMode == "insert"){
			if($pemesanan->insert()){
				$reqId = $this->db->query("select pemesanan_id from pemesanan where user_login_id='$this->USER_LOGIN_ID' 
					order by pemesanan_id desc limit 1")->row()->pemesanan_id;

				for ($i=0; $i < count(array_filter($reqTitleGender)); $i++) { 
					$pemesanan = new Pemesanan();
					$pemesanan->setField("PEMESANAN_ID", $reqId);
					$pemesanan->setField("TITLE_GENDER", $reqTitleGender[$i]);
					$pemesanan->setField("NAMA", $reqNamaPenumpang[$i]);
					$pemesanan->setField("TIPE_IDENTITAS", $reqTipeIdentitas[$i]);
					$pemesanan->setField("NOMOR_IDENTITAS", $reqNomerIdentitas[$i]);
					$pemesanan->insertPenumpang();
				}

				$this->db->query("update jadwal set kuota=(kuota - $reqJumlahPenumpang) where jadwal_id='$reqJadwalId' ");

				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}
		else{
			if($pemesanan->update()){
				echo "BERHASIL|Data berhasil diubah";

				$this->db->query("delete from penumpang where pemesanan_id='$reqId' ");
				for ($i=0; $i < count(array_filter($reqTitleGender)); $i++) { 
					$pemesanan = new Pemesanan();
					$pemesanan->setField("PEMESANAN_ID", $reqId);
					$pemesanan->setField("TITLE_GENDER", $reqTitleGender[$i]);
					$pemesanan->setField("NAMA", $reqNamaPenumpang[$i]);
					$pemesanan->setField("TIPE_IDENTITAS", $reqTipeIdentitas[$i]);
					$pemesanan->setField("NOMOR_IDENTITAS", $reqNomerIdentitas[$i]);
					$pemesanan->insertPenumpang();
				}
			}
			else{
				echo "GAGAL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("Pemesan");
		$pemesan = new Pemesan();
		
		$reqId	= $this->input->post("reqId");

		$pemesan->setField("PEMESANAN_ID", $reqId);
		if($pemesan->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}
		
}
?>