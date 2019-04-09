<?php

function mc_connect($url, $json_data, $patch = true) {
	global $MC_apikey;
	
	$auth = base64_encode( 'user:'.$MC_apikey );
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '. $auth));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	if($patch)	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	//return curl_exec($ch);
	$dump = curl_exec($ch);
}



function mc_subscribe($email, $fname, $lname) {
	global $MC_listid, $MC_server;
	
	$data = array(
		'email_address' => $email,
		'status'        => 'subscribed',
		'merge_fields'  => array(
			'FNAME' => $fname,
			'LNAME' => $lname
			)
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/';
	mc_connect($url, $json_data, false);
}

function mc_unsubscribe($email) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'status'        => 'unsubscribed',
		);
	$json_data = json_encode($data);

	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data);
}

function mc_addinterest($email, $interest) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'interests' => array(
			$interest => true
			)
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data);
}

function mc_addtag($email, $tag) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'name' => $tag,
		'status' => 'active'		
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid .'/tags';
	mc_connect($url, $json_data);
}


function mc_rminterest($email, $interest) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'interests' => array(
			$interest => false
			)
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data);
}


function mc_rmtag($email, $tag) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'name' => $tag,
		'status' => 'inactive'		
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid .'/tags';
	mc_connect($url, $json_data);
}

function mc_changename($email, $fname, $lname) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'merge_fields'  => array(
			'FNAME' => $fname,
			'LNAME' => $lname
			)
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data);
}

function mc_changemail($email, $newEmail) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'email_address' => $newEmail,
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data);
}
