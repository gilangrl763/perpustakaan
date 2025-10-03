<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kauth
 *
 * @author user
 */
class usermobile{
	// var $USER_LOGIN_ID;
	
	var $ID;
	var $NAMA;
	var $CABANG_ID;
	var $CABANG;
	var $JABATAN;
	var $USERNAME;
	var $USER_LOGIN_ID;
	var $USER_LOGIN;
	var $PEGAWAI_ID;
	var $SATUAN_KERJA_ID_ASAL;
	var $SATUAN_KERJA_ASAL;
	
    /******************** CONSTRUCTOR **************************************/
    function usermobile(){
	
		 $this->emptyProps();
    }

    /******************** METHODS ************************************/
    /** Empty the properties **/
    function emptyProps(){
		$this->ID = "";
		$this->NAMA = "";
		$this->CABANG_ID = "";
		$this->CABANG = "";
		$this->JABATAN = "";
		$this->USERNAME = "";
		$this->USER_LOGIN_ID = "";
		$this->USER_LOGIN = "";
		$this->PEGAWAI_ID = "";
		$this->SATUAN_KERJA_ID_ASAL = "";
		$this->SATUAN_KERJA_ASAL = "";
				
    }
		
    
    /** Verify user login. True when login is valid**/
    function getInfo($userLoginId, $reqToken){			
		$CI =& get_instance();

		$CI =& get_instance();
		$CI->load->model("Users");	
		// $CI->load->model("LogPengunjung");
		// $log_pengunjung = new LogPengunjung();	
		
		
		$users = new Users();
		$users->selectByIdPasswordMobile($userLoginId);
		
		if($users->firstRow())
		{
			
            $this->ID = $users->getField("PEGAWAI_ID");
            $this->NAMA = $users->getField("NAMA");
            $this->CABANG_ID = $users->getField("SATUAN_KERJA_ID");
            $this->CABANG = $users->getField("SATUAN_KERJA");
            $this->JABATAN = $users->getField("JABATAN");
            $this->USERNAME = $users->getField("PEGAWAI_ID");
            $this->USER_LOGIN_ID = $users->getField("PEGAWAI_ID");
            $this->USER_LOGIN = $users->getField("USER_LOGIN");
            $this->PEGAWAI_ID = $users->getField("PEGAWAI_ID");
            $this->SATUAN_KERJA_ID_ASAL = $users->getField("SATUAN_KERJA_ID_ASAL");


			$CI->load->model("SatuanKerja");	
			$satuan_kerja = new SatuanKerja();
			$satuan_kerja->selectByParamsSimple(array("SATUAN_KERJA_ID" => $users->getField("SATUAN_KERJA_ID_ASAL")));
			$satuan_kerja->firstRow();
            $this->SATUAN_KERJA_ASAL = $satuan_kerja->getField("NAMA");
						
		}

    }
			   
}
	
  /***** INSTANTIATE THE GLOBAL OBJECT */
  $userMobile = new usermobile();

?>
