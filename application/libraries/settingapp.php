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
  class SettingApp{

	var $currentRow;
	
    /******************** CONSTRUCTOR **************************************/
    function SettingApp(){
		$CI =& get_instance();
		$CI->load->model("SettingAplikasi");
		
		$setting_aplikasi = new SettingAplikasi();
		$setting_aplikasi->selectByParams();
		while($setting_aplikasi->nextRow())	
		{
			$this->currentRow[strtolower($setting_aplikasi->getField("NAMA"))] = $setting_aplikasi->getField("NILAI");
		}
    }
	
	public function getSetting($fieldName){
		$fieldName = strtolower($fieldName);
		
		return $this->currentRow[$fieldName];
	}

}
	
  /***** INSTANTIATE THE GLOBAL OBJECT */
  $settingApp = new SettingApp();

?>