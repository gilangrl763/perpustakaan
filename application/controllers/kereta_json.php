<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class kereta_json extends CI_Controller {

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
		$this->KODE = $this->kauth->getInstance()->getIdentity()->KODE;       
		$this->NAMA = $this->kauth->getInstance()->getIdentity()->NAMA;           
	}	
	
	function add() 
	{
		$this->load->model("Kereta");
		$kereta		= new Kereta();

		$reqId						= $this->input->post("reqId");
		$reqMode					= $this->input->post("reqMode");
		$reqKode					= $this->input->post("reqKode");
		$reqNama					= $this->input->post("reqNama");
		

		$kereta->setField("KERETA_ID", $reqId);
		$kereta->setField("KODE", $reqKode);
		$kereta->setField("NAMA", $reqNama);
		$kereta->setField("CREATED_BY", $this->USER_LOGIN_ID);
		$kereta->setField("UPDATED_BY", $this->USER_LOGIN_ID);
		if($reqMode == "insert"){
			if($kereta->insert()){
				echo "BERHASIL|Data berhasil disimpan";
			}
			else{
				echo "GAGAL|Data gagal disimpan";
			}
		}
		else{
			if($kereta->update()){
				echo "BERHASIL|Data berhasil diubah";
			}
			else{
				echo "GAGAL|Data gagal diubah";
			}
		}
	}


	function delete() 
	{
		$this->load->model("Kereta");
		$kereta	= new Kereta();
		
		$reqId	= $this->input->post("reqId");

		$kereta->setField("KERETA_ID", $reqId);
		if($kereta->delete()){
			echo "BERHASIL|Data berhasil dihapus";
		}
		else{
			echo "BERHASIL|Data gagal dihapus";
		}
	}

	function combo() 
	{
		$this->load->model("Kereta");
		$kereta	= new Kereta();

		$i = 0;
		$arr_json = array();
		$kereta->selectByParams(array());
		while($kereta->nextRow())
		{
			$arr_json[$i]['id']		= $kereta->getField("KERETA_ID");
			$arr_json[$i]['text']	= $kereta->getField("NAMA");
			$arr_json[$i]['KODE']	= $kereta->getField("KODE");
			$i++;
		}
		
		echo json_encode($arr_json);
	}
		
}
?>