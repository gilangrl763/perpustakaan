<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class user_login_json extends CI_Controller {

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
		$this->NAMA = $this->kauth->getInstance()->getIdentity()->NAMA;       
		$this->EMAIL = $this->kauth->getInstance()->getIdentity()->EMAIL;       
		$this->STATUS = $this->kauth->getInstance()->getIdentity()->STATUS;       
	}	
	
	function add() 
	{
		$this->load->model("UserLogin");
		$user_login	= new UserLogin();
		
		$reqId			= $this->input->post("reqId");
		$reqMode		= $this->input->post("reqMode");
		$reqNama		= $this->input->post("reqNama");
		$reqEmail		= $this->input->post("reqEmail");
		$reqUserTypeId	= $this->input->post("reqUserTypeId");
		$reqStatus		= $this->input->post("reqStatus");

		if($reqStatus == ""){
			$reqStatus = "1";
		}

		$reqUserType = "";
		for ($i=0; $i < count($reqUserTypeId) ; $i++) { 
			if($reqUserType == ""){
				$reqUserType = (string)$reqUserTypeId[$i];
			}
			else{
				$reqUserType .= ",".(string)$reqUserTypeId[$i];
			}
		}

		if($reqUserType == ""){
			echo "GAGAL|Hak akses belum ditentukan";
			return;
		}
		

		$user_login->setField("USER_LOGIN_ID", $reqId);
		$user_login->setField("USER_TYPE_ID", $reqUserType);
		$user_login->setField("NAMA", $reqNama);
		$user_login->setField("EMAIL", $reqEmail);
		$user_login->setField("USER_LOGIN", $reqEmail);
		$user_login->setField("USER_PASSWORD", $reqPassword);
		$user_login->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$user_login->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		$user_login->setField("FOTO", $reqFoto);
		$user_login->setField("STATUS", $reqStatus);
		if($reqMode == "insert"){
			if($user_login->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "BERHASIL|Data gagal disimpan";
			}
		}
		else{
			if($user_login->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "BERHASIL|Data gagal diubah";
			}
		}
	}


	function ubah_password() 
	{
		$this->load->model("UserLogin");
		$user_login	= new UserLogin();

		$reqPassword	= $this->input->post("reqPassword");
		$reqKonfirmasiPassword	= $this->input->post("reqKonfirmasiPassword");

		if($reqPassword != $reqKonfirmasiPassword){
			echo "GAGAL|Konfirmasi password berbeda, silahkan ulangi kembali";
			return;
		}

		$user_login->setField("USER_LOGIN_ID", $this->USER_LOGIN_ID);
		$user_login->setField("FIELD", "USER_PASSWORD");
		$user_login->setField("FIELD_VALUE", md5($reqPassword));
		$user_login->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($user_login->updateByField()){
			echo "BERHASIL|Berhasil mengubah password";
		}
		else{
			echo "BERHASIL|Gagal mengubah password";
		}
	}

	function delete() 
	{
		$this->load->model("UserLogin");
		$user_login	= new UserLogin();
		
		$reqId	= $this->input->post("reqId");

		$user_login->setField("USER_LOGIN_ID", $reqId);
		if($user_login->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "GAGAL|Data gagal dihapus";
		}
	}
		
}
?>