<?php

# https://github.com/actuallymentor/MailChimp-API-v3.0-PHP-cURL-example/blob/master/mc-API-connector.php

function mc_connect($url, $json_data, $type, $output = false) {
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
	$dump = curl_exec($ch);
		
	if($output) {
		return $dump;
	} else {
		if(strpos($dump, 'error-glossary')) {
			return false;
		} else {
			return true;
		}		
	}
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
	return mc_connect($url, $json_data, 'post');
}



function mc_unsubscribe($email) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'status'        => 'unsubscribed',
		);
	$json_data = json_encode($data);

	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	return mc_connect($url, $json_data, 'patch');
}



function mc_resubscribe($email) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'status'        => 'subscribed',
		);
	$json_data = json_encode($data);

	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	return mc_connect($url, $json_data, 'patch');
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
	return mc_connect($url, $json_data, 'patch');
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
	return mc_connect($url, $json_data, 'patch');
}



function mc_addtag($email, $segment_id) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array("email_address" => $email);
	
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/segments/'. $segment_id .'/members';
	return mc_connect($url, $json_data, 'post');
}



function mc_rmtag($email, $segment_id) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array();
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/segments/'. $segment_id .'/members/'. $userid;
	return mc_connect($url, $json_data, 'delete');
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
	return mc_connect($url, $json_data, 'patch');
}



function mc_changemail($email, $newEmail) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array(
		'email_address' => $newEmail,
		);
	$json_data = json_encode($data);
	
	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	return mc_connect($url, $json_data, 'patch');
}


function mc_getData($email) {
	global $MC_listid, $MC_server;
	
	$userid = md5( strtolower( $email ) );
	$data = array();
	$json_data = json_encode($data);

	$url = 'https://'.$MC_server.'api.mailchimp.com/3.0/lists/'.$MC_listid.'/members/' . $userid;
	$result = mc_connect($url, $json_data, 'get', true);
	$json = json_decode($result, true);
		
	$data['voornaam']	= $json['merge_fields']['VOORNAAM'];
	$data['tussen']		= $json['merge_fields']['TUSSENVOEG'];
	$data['achter']		= $json['merge_fields']['ACHTERNAAM'];
	$data['status']		= $json['status'];
	
	foreach($json['tags'] as $value) {
		$id = $value['id'];
		$data['tags'][$id] = $value['name'];
	}
	
	return $data;
}

?>