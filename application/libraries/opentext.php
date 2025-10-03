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
  class OpenText{
	var $id;
	
    /******************** CONSTRUCTOR **************************************/
    function opentext(){
	
		 $this->emptyProps();
    }

    /******************** METHODS ************************************/
    /** Empty the properties **/
    function emptyProps(){
		$this->id = "";
    }
		
    
    /** Verify user login. True when login is valid**/
    function get($prNumberRequest){			
return;
		$CI =& get_instance();
		$CI->load->model("gateway/SapPrOpentext");	
		$sap_pr_opentext = new SapPrOpentext();

		$openIdRequest = $sap_pr_opentext->getId($prNumberRequest);

		#Send Reponse To FireBase Server	
		$username = 'ARIF';
		$password = "P3lindo21!";

		$action = "POST";
		$url = "http://svrldot002.pelindo.co.id:8080/OTCS/cs/api/v1/auth";
		$parameters = array("username" => "ARIF", "password" => "P3lindo21!");
		$result = callAPI($action, $url, $parameters);
		$arr = json_decode($result, true);
		$ticket = $arr["ticket"];
				
		if($openIdRequest == "")
		{
			
			$action = "GET";
			$url = "http://svrldot002.pelindo.co.id:8080/OTCS/cs/api/v2/nodes/23036/nodes";
			$parameters = array("OTCSTICKET" => $ticket);
			$result = callAPI($action, $url, $parameters);
			$arr = json_decode($result, true);
			$arrData = $arr["results"];
			
			for($i=0;$i<count($arrData);$i++)
			{
				$prNumber = $arrData[$i]["data"]["categories"][0]["22929_3"];
				$openId = $arrData[$i]["data"]["properties"]["id"];
							
				$sap_pr_opentext = new SapPrOpentext();
				$sap_pr_opentext->setField("PR_NUMBER", $prNumber);
				$sap_pr_opentext->setField("OPENTEXT_ID", $openId);
				$sap_pr_opentext->insert();
				unset($sap_pr_opentext);
				
				if($prNumberRequest == $prNumber)
					$openIdRequest = $openId;
				
			}
		}
		
		if($openIdRequest == "")
			return array();
		else
		{
			$arrData = array();
			
			$action = "GET";
			$url = "http://svrldot002.pelindo.co.id:8080/OTCS/cs/api/v2/nodes/".$openIdRequest."/nodes";
			$parameters = array("OTCSTICKET" => $ticket);
			$result = callAPI($action, $url, $parameters);
			$arr = json_decode($result, true);
			$arrData = $arr["results"];
			
			for($i=0;$i<count($arrData);$i++)
			{
				$openFileId = $arrData[$i]["data"]["properties"]["id"];
				$openFileName = $arrData[$i]["data"]["properties"]["name"];
							
				$arrData[$i]["id"] = $openFileId;
				$arrData[$i]["name"] = $openFileName;
				$arrData[$i]["ticket"] = $ticket;
			}
			
			return $arrData;
			
						
		}
		
		  
    }

    function download($fileId){			

		#Send Reponse To FireBase Server	
		$username = 'ARIF';
		$password = "P3lindo21!";

		$action = "POST";
		$url = "http://svrldot002.pelindo.co.id:8080/OTCS/cs/api/v1/auth";
		$parameters = array("username" => "ARIF", "password" => "P3lindo21!");
		$result = callAPI($action, $url, $parameters);
		$arr = json_decode($result, true);
		$ticket = $arr["ticket"];
		$action = "GET";
		$url = "http://svrldot002.pelindo.co.id:8080/OTCS/cs/api/v1/nodes/".$fileId."/content";
		
		$parameters = array("OTCSTICKET" => $ticket);
		$result = callAPI($action, $url, $parameters, $ticket);
		
		return  $result;
			
	}
  }

function callAPI($method, $url, $data, $token=""){
	$curl = curl_init();
	
	switch ($method){
	  case "POST":
		 curl_setopt($curl, CURLOPT_POST, 1);
		 if ($data)
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		 break;
	  case "PUT":
		 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		 if ($data)
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
		 break;
	  default:
		 if ($data)
			$url = sprintf("%s?%s", $url, http_build_query($data));
	}
	
	// OPTIONS:
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	  'OTCSTICKET: '.$token
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	
	// EXECUTE:
	$result = curl_exec($curl);
	if(!$result){die("Connection Failure");}
	curl_close($curl);
	return $result;
}

/***** INSTANTIATE THE GLOBAL OBJECT */
$openText = new OpenText();

?>