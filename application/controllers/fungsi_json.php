<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once("functions/string.func.php");
include_once("functions/date.func.php");

class fungsi_json extends CI_Controller {

	function __construct() {
		parent::__construct();
		
		//kauth
		if (!$this->kauth->getInstance()->hasIdentity())
		{
			// trow to unauthenticated page!
			//redirect('Login');
		}       
		
		/* GLOBAL VARIABLE */
		$this->USER_LOGIN_ID = $this->kauth->getInstance()->getIdentity()->USER_LOGIN_ID;   
	}	
	
	
	function check_email()
	{
		$reqParam1 = $this->input->post("reqParam1");
		
		if($reqParam1 == ""){
			exit;
		}
		
		$adaData = $this->db->query("select count(subscribe_email_id) as subscribe_email_id from subscribe_email where email='$reqParam1' ")->row()->subscribe_email_id;
		if($adaData == 0){
			echo "true";
		}
		else{
			echo "false";
		}
	}		
}
?>
