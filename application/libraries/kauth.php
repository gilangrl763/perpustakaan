<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'kloader.php';
include_once("libraries/nusoap-0.9.5/lib/nusoap.php");
include_once("functions/string.func.php");
include_once("functions/date.func.php");

class kauth {

    private $ldap_config = array('server1'=>array('host'=>'10.0.0.11',
        'useStartTls'=>false,
        'accountDomainName'=>'pp3.co.id',
        'accountDomainNameShort'=>'PP3',
        'accountCanonicalForm'=>3,
        'baseDn'=>"DC=pp3,DC=co,DC=id"));


        function __construct() {
			//load the auth class
	        kloader::load('Zend_Auth');
	        kloader::load('Zend_Auth_Storage_Session');
	        
			//set the unique storege
	        Zend_Auth::getInstance()->setStorage(new Zend_Auth_Storage_Session("b1m4Mark3tPl4c3"));
    	}
    
	public function localAuthenticate($username,$password) {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
		
		$CI =& get_instance();
		$CI->load->model("UserLogin");
		
		$user_login = new UserLogin();
		$user_login->selectByIdPassword($username, md5($password));
		// echo $user_login->query;return;
		if($user_login->firstRow())
		{
			// $ip_address = _ip();

			// $CI->db->query("update user_login set login_time=current_timestamp, ip_address='$ip_address' 
			// 	where user_login_id='".$user_login->getField("USER_LOGIN_ID")."' ");

            $identity = new stdClass();
            $identity->ID = $user_login->getField("USER_LOGIN_ID");
            $identity->USER_LOGIN_ID = $user_login->getField("USER_LOGIN_ID");
            $identity->USER_GROUP = $user_login->getField("USER_GROUP");
            $identity->EMAIL = $user_login->getField("EMAIL");
            $identity->STATUS = $user_login->getField("STATUS");

            $auth->getStorage()->write($identity);

			return "1";
		}
		else
		{
			return 'Username/E-mail atau Password salah.';
		}
    }


    public function multiAkses($groupId) {
      		
        $auth = Zend_Auth::getInstance();
        $CI =& get_instance();
		
		$CI->load->model("UserType");

		$identity = new stdClass();
		$identity->USER_LOGIN_ID = $auth->getIdentity()->USER_LOGIN_ID;
		$identity->USER_LOGIN = $auth->getIdentity()->USER_LOGIN;
		$identity->USER_STATUS = "1";
		$identity->USER_NAMA = $auth->getIdentity()->USER_NAMA;
		$identity->KD_DIREKTORAT = $auth->getIdentity()->KD_DIREKTORAT;
		$identity->ANAK_PERUSAHAAN = "0";
		
		$identity->ID = $auth->getIdentity()->ID;
		$identity->UNIT_KERJA_ID = $auth->getIdentity()->UNIT_KERJA_ID;
		$identity->UNIT_KERJA = $auth->getIdentity()->UNIT_KERJA;
		$identity->NIP = $auth->getIdentity()->NIP;
		$identity->LOGIN_TIME = time();
		$identity->LOGIN_DATE = date("l, j M Y, H:i",time());
		
        $identity->ANAK_PERUSAHAAN = $auth->getIdentity()->ANAK_PERUSAHAAN;
			
		$identity->HAKAKSES = $auth->getIdentity()->HAKAKSES;
		$identity->HAKAKSES_DESC = $auth->getIdentity()->HAKAKSES_DESC;
				
		$OuserLevel = new UserType();
		$OuserLevel->selectByParams(array('NAMA'=> $groupId));
		if(!$OuserLevel->firstRow())
		{
		   return "Role tidak ditemukan!!";
		}		
		
		$identity->USER_TYPE = $OuserLevel->getField('NAMA');
		$identity->USER_TYPE_ID = $OuserLevel->getField('USER_TYPE_ID');
		 
		$auth->getStorage()->write($identity);
		return "1";						
		
    }
	
	
	
    public function getInstance(){
        return Zend_Auth::getInstance();
    }

    public function getToken($reqUserLoginId)
    {
    	$user_login_web = new UserLoginWeb();
		$user_login_web->setField("USER_LOGIN_ID", trim($reqUserLoginId));
		$user_login_web->setField("WAKTU_LOGIN", "CURRENT_TIMESTAMP");
		$user_login_web->setField("STATUS", "1");
		$user_login_web->insert();

		return $user_login_web->idToken;
    }

    public function subscribeTopics($topic, $token)
    {
    	$curlUrl = "https://iid.googleapis.com/iid/v1:batchAdd";
		$mypush = array(
			"to" => "/topics/".$topic, 
			"registration_tokens" => array($token)
		);
		$myjson = json_encode($mypush);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curlUrl);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POST, True);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $myjson);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:key='. API_ACCESS_KEY));
		//getting response from server
		$response = curl_exec($ch);
		// var_dump($response); exit();
    }


    public function unSubscribeTopics()
    {
    	$auth = Zend_Auth::getInstance();
    	$topic = $auth->getIdentity()->ID;
    	$token = $auth->getIdentity()->TOKEN_FIREBASE;

    	$curlUrl = "https://iid.googleapis.com/iid/v1:batchRemove";
		$mypush = array(
			"to" => "/topics/".$topic, 
			"registration_tokens" => array($token)
		);
		$myjson = json_encode($mypush);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $curlUrl);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POST, True);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $myjson);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:key='. API_ACCESS_KEY));
		//getting response from server
		$response = curl_exec($ch);
    }
}

?>
