<?php
	include("include/init.php");
	loadlib("http");
    loadlib("browserid");

    $browserID = new BrowserID($_SERVER['HTTP_HOST'], $_REQUEST['assertion']);

    if($browserID->verify_assertion()) {
 
       echo json_encode(array('status'=>'okay', 'email'=>$browserID->getEmail()));

    } else {

       echo json_encode(array('status'=>'failure','reason'=>$browserID->getReason()));
    }

?>