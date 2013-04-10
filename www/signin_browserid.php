<?php
	include("include/init.php");
	
	loadlib("http");
    loadlib("browserid");
	loadlib("random");

   	function send_json($data){
	
		$json = json_encode($data);
		
		header("Content-type: text/javascript");
		header("Content-length: " . strlen($json));
		
		echo $json;
		exit();
	}
	
	features_ensure_enabled("signin");
	
	if ($GLOBALS['cfg']['user']['id']){
		$rsp = array('status' => 'fail');
		send_json($rsp);
	}

	// now we do the verify assertion thing

	$browserID = new BrowserID($_SERVER['HTTP_HOST'], $_REQUEST['assertion']);
	
    if (! $browserID->verify_assertion()){
		$rsp = array('status' => 'fail');
		send_json($rsp);
	}

	// check if we already have a user account
	$user = users_get_by_email($browserID->getEmail());
	
	if (!$user['id']){
			// keep real simple for now... if there is no user, create one
			$email = $browserID->getEmail();
			$username = explode("@",$email);
			$password = random_string();
			
			$user = users_create_user(array(
				"username" => $username[0],
				"email" => $email,
				"password" => $password,
			));	
			
			$user = $user['user'];	
	   }
		
	# SET COOKIES HERE?
	
	# OMG - DO NOT LEAVE ME HERE - MAKE SURE THIS IS ONLY EVER PART OF lib_login
	# something like login_set_login_cookie()
	
	$expires = ($GLOBALS['cfg']['enable_feature_persistent_login']) ? strtotime('now +10 years') : 0;

	$auth_cookie = login_generate_auth_cookie($user);
	login_set_cookie($GLOBALS['cfg']['auth_cookie_name'], $auth_cookie, $expires);
	
	$rsp = array('status' => 'okay');
	send_json($rsp);
	
?>