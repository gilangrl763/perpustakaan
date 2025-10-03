<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("application/libraries/google-api-php-client-2.4.0/vendor/autoload.php");
// require_once 'application/libraries/google-api-php-client/src/Google/autoload.php';

class GoogleClient{
	var $client;
	var $cal;

	function GoogleClient()
	{

    }

	function getClient($authCode = null)
	{
		$CI =& get_instance();
		$auth = $CI->kauth->getInstance();
		$identity = $auth->getIdentity();
		
		$CI->load->model("UserCalendar");
		$user_calendar = new UserCalendar();

		$token = $user_calendar->getTokenGoogle($identity->ID);

		$client = new Google_Client();
        $client->setClientId('442696280058-se85nnpfk4fgbm46gr3krp8n9nau4q7u.apps.googleusercontent.com');
        $client->setClientSecret('xM-enLJwKt4YjLm8WyuE2Ze_');
        $client->setRedirectUri(base_url().'web/meeting_json/auth');
        $client->setScopes('https://www.googleapis.com/auth/calendar');
        $client->setApprovalPrompt('force');
        $client->setAccessType('offline');

	    if ($token <> "") {
	        $accessToken = json_decode($token, true);
	        $client->setAccessToken($accessToken);
	        // return $client;
	    }

	    if(isset($authCode)){
	    	try{
	    		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
	    		$client->setAccessToken($accessToken);
	    		$user_calendar->setTokenGoogle($identity->ID, json_encode($client->getAccessToken()));
	    	}catch(Exception $e){
	    		$authUrl = $client->createAuthUrl();
				header("Location: $authUrl"); 
				exit();
	    	}
	    	
           

            // Check to see if there was an error.
            // if (array_key_exists('error', $accessToken)) {
            //     throw new Exception(join(', ', $accessToken));
            // }

            

            // Save the token to a file.
	        
	    }

	    // If there is no previous token or it's expired.
	    // echo($client->isAccessTokenExpired());
	    if ($client->isAccessTokenExpired()) {
	        if ($client->getRefreshToken()) {
	        	try{
		    		$accessToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
		    		$client->setAccessToken($accessToken);
		    		// file_put_contents($tokenPath, json_encode($client->getAccessToken()));
		   			// $identity->TOKEN_GOOGLE = json_encode($client->getAccessToken());
					// $auth->getStorage()->write($identity);
					$user_calendar->setTokenGoogle($identity->ID, json_encode($client->getAccessToken()));
		    	}catch(Exception $e){
		    		$authUrl = $client->createAuthUrl();
	            	//return redirect($authUrl);
					header("Location: $authUrl"); 
					exit();
		    	}
	            
	        } else {
	            $authUrl = $client->createAuthUrl();
				
				header("Location: $authUrl"); 
				exit();
	            
	        }
	       
	    }



        return $client;
	}

	function getSimpleClient()
	{
		$CI =& get_instance();
		$auth = $CI->kauth->getInstance();
		$identity = $auth->getIdentity();

		$CI->load->model("UserCalendar");
		$user_calendar = new UserCalendar();

		$token = $user_calendar->getTokenGoogle($identity->ID);
		
		$client = new Google_Client();
        $client->setClientId('442696280058-se85nnpfk4fgbm46gr3krp8n9nau4q7u.apps.googleusercontent.com');
        $client->setClientSecret('xM-enLJwKt4YjLm8WyuE2Ze_');
        $client->setRedirectUri(base_url().'web/meeting_json/auth');
        $client->setScopes('https://www.googleapis.com/auth/calendar');
        $client->setApprovalPrompt('force');
        $client->setAccessType('offline');

	    if ($token <> "") {
	        $accessToken = json_decode($token, true);
	        $client->setAccessToken($accessToken);
	    }
	    return $client;
	}

	function getCalendarService()
	{
		$this->cal = new Google_Service_Calendar($this->client);
	}

}
?>