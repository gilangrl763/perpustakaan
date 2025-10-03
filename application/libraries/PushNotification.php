<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'kloader.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Kauth
 *
 * @author user
 */

define( 'API_ACCESS_KEY', 'AAAAEDRHfrg:APA91bFAVakF2JvGNaLizZ3_XW90EPF-Wfz2vEpGV7cKmF4hYpasRlIWMl0JVzYy0KUqAtrNYF8TWG0b41kmPaUGbvS2YnX71B0gcW8zncKmLo2GQ6wsquBk7MJMu2oXYhyrRJuZp05X');

class PushNotification{
	var $tokenFirebase; 
	var $type;
	var $id;
	var $jenis;
	var $title;
	var $body;
	
    /******************** CONSTRUCTOR **************************************/
    function PushNotification(){
		 $this->emptyProps();
    }

    /******************** METHODS ************************************/
    /** Empty the properties **/
    function emptyProps(){
		$this->tokenFirebase = "";
		$this->type = "";
		$this->id = "";
		$this->jenis = "";
		$this->title = "";
		$this->body = "";

    }

    /** Verify user login. True when login is valid**/
    function send_notification_v2($data){
    	// echo 'Hello';

		#prep the bundle
		$msg = array
		(
			'body' 	=> $data['body'],
			'title'	=> $data['title'],
			'sound' => 'default',
			'icon'	=>'default'
		);

		$fields = array
		(
			'to'			=> $data['to'],
			'notification'	=> $msg,
			'data'			=> $data['data']
		);
		
		$headers = array
		(
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		);
		
		#Send Reponse To FireBase Server	
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		// echo $result;

		$this->hasil = $result;

		curl_close( $ch );

	}

	function send_notification_bak($tokenFirebase, $type, $id, $jenis, $title, $body){
    	// echo 'Hello';

		#prep the bundle
		$msg = array
		(
			'body' 	=> $body,
			'title'	=> $title,
			'sound' => 'default',
			'icon'	=>'default'
		);
		
		$data = array
		(
			'type'	=> $type,
			'id'	=> $id,
			'jenis'	=> $jenis
		);

		$fields = array
		(
			'to'			=> $tokenFirebase,
			'notification'	=> $msg,
			'data'			=> $data
		);
		
		$headers = array
		(
			'Authorization: key=' . API_ACCESS_KEY,
			'Content-Type: application/json'
		);
		
		#Send Reponse To FireBase Server	
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		// echo $result;

		$this->hasil = $result;

		curl_close( $ch );

	}

    
			   
}
	
  /***** INSTANTIATE THE GLOBAL OBJECT */
  $pushNotification = new PushNotification();

?>
