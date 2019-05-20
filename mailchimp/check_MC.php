<?php
include_once('../include/functions.php');
include_once('../include/MC_functions.php');
include_once('../include/config.php');

$db = connect_db();

# 1000 / 25 = 40

$count = 25;
$offset = ((fmod(date('z'),2)*24)+date('G'))*$count;

$data = mc_getmembers($offset, $count);

if(count($data) > 0) {
	foreach($data as $rij) {
		# 3 seconden per persoon moet voldoende zijn
		set_time_limit(3);
	
		$email = $rij['email'];
					
		$sql = "SELECT * FROM $TableMC WHERE $MCmail like '$email'";
		$result = mysqli_query($db, $sql);
				
		if(mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_array($result);
			$wijk	=	$row[$MCwijk];			
			$segment_id = $tagWijk[$wijk];
			
			/*
			echo $row[$MCID] .'|'. $rij['scipio'] .'<br>';
			echo $row[$MCstatus] .'|'. $rij['status'] .'<br>';
			echo $row[$MCfname] .'|'. $rij['voornaam'] .'<br>';
			echo $row[$MCtname] .'|'. $rij['tussen'] .'<br>';	
			echo $row[$MClname] .'|'. $rij['achter'] .'<br>';	
			*/
						
			if($row[$MCID] != $rij['scipio'] AND $row[$MCstatus] = 'subscribed')		toLog('error', '', $row[$MCID], "ScipioID in MailChimp (".$rij['scipio'].") en lokale database (". $row[$MCID] .") komen niet overeen ($email)");			
			if($row[$MCstatus] != $rij['status'] AND $row[$MCstatus] != 'block')		toLog('error', '', $row[$MCID], "Volgens MailChimp is $email ". $rij['status'] .", volgende de lokale database niet");			
			if($row[$MCfname] != $rij['voornaam'])																	toLog('error', '', $row[$MCID], "Volgens Mailchimp is de voornaam van $email ". $rij['voornaam'] .", volgens de lokale database ". $row[$MCfname]);
			if(urldecode($row[$MCtname]) != $rij['tussen'])													toLog('error', '', $row[$MCID], "Volgens Mailchimp is het tussenvoegsel van $email .". $rij['tussen'] .", volgens de lokale database ". $row[$MCtname]);
			if($row[$MClname] != $rij['achter'])																		toLog('error', '', $row[$MCID], "Volgens Mailchimp is de achternaam van $email ". $rij['achternaam'] .", volgens de lokale database ". $row[$MClname]);
			if(!array_key_exists($tagScipio, $rij['tags']))													toLog('error', '', $row[$MCID], "Scipio-tag ontbreekt in MailChimp ($email staat wel in lokale database)");
			if(!array_key_exists($segment_id, $rij['tags']) AND $wijk != '')				toLog('error', '', $row[$MCID], "Wijk-tag (wijk $wijk) ontbreekt in MailChimp ($email staat wel in lokale database)");
		} elseif(mysqli_num_rows($result) > 1) {
			toLog('error', '', $rij['scipio'], "$email komt meer dan 1x voor in de lokale database");
		} elseif(array_key_exists($tagScipio, $rij['tags']) AND $rij['status'] != 'unsubscribed') {
			toLog('error', '', $rij['scipio'], $rij['scipio'] ." komt wel voor in MailChimp, maar niet in lokale database");
		//} else {
		//	toLog('debug', '', '', "$email niet lokaal gevonden, maar lijkt geen probleem");
		}
	}	
}

toLog('info', '', '', "Data vanuit MailChimp naast de lokale database gelegd ($offset, $count)");

?>
