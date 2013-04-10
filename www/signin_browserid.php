<?php
	include("include/init.php");
    loadlib("browserid");
	loadlib("random");

    $browserID = new BrowserID($_SERVER['HTTP_HOST'], $_REQUEST['assertion']);
	
	
    if($browserID->verify_assertion()) {

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
		   	
	   }
		
	   login_do_login($user);
	   exit();
	
    } else {

       echo json_encode(array('status'=>'failure','reason'=>$browserID->getReason()));
    }

?>