<?php

function mc_connect($url, $json_data, $type) {
	global $MC_apikey;
	
	$auth = base64_encode( 'user:'.$MC_apikey );
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '. $auth));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	if($type == 'patch')	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
	if($type == 'post')		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	if($type == 'get')		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	if($type == 'delete')	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	//echo curl_exec($ch);
	//return curl_exec($ch);
	$dump = curl_exec($ch);
}



function mc_subscribe($email, $fname, $tname, $lname) {
	global $MC_listid, $MC_server;
	
	$data = array(
		'email_address' => $email,
		'status'        => 'subscribed',
		'merge_fields'  => array(
			'VOORNAAM' => $fname,
			'TUSSENVOEG' => $tname,
			'ACHTERNAAM' => $lname
			)
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/';
	mc_connect($url, $json_data, 'post');
}



function mc_unsubscribe($email) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'status'        => 'unsubscribed',
		);
	$json_data = json_encode($data);

	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data, 'patch');
}



function mc_resubscribe($email) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'status'        => 'subscribed',
		);
	$json_data = json_encode($data);

	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data, 'patch');
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
	mc_connect($url, $json_data, 'patch');
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
	mc_connect($url, $json_data, 'patch');
}



function mc_addtag($email, $segment_id) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array("email_address" => $email);
	
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/segments/'. $segment_id .'/members';
	mc_connect($url, $json_data, 'post');
}



function mc_rmtag($email, $segment_id) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array();
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/segments/'. $segment_id .'/members/'. $userid;
	mc_connect($url, $json_data, 'delete');
}



function mc_changename($email, $fname, $tname, $lname) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'merge_fields'  => array(
			'VOORNAAM' => $fname,
			'TUSSENVOEG' => $tname,
			'ACHTERNAAM' => $lname
			)
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data, 'patch');
}



function mc_changemail($email, $newEmail) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'email_address' => $newEmail,
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	mc_connect($url, $json_data, 'patch');
}
