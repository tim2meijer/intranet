<?php
include_once('../include/functions.php');
include_once('../include/MC_functions.php');
include_once('../include/config.php');

# https://github.com/actuallymentor/MailChimp-API-v3.0-PHP-cURL-example/blob/master/mc-API-connector.php

$db = connect_db();

# Even alle adressen markeren om te verwijderen
# Bij elk adres wat we zometeen wel zien wordt deze markerking weggehaald
$sql_mc = "UPDATE $TableMC SET $MCmark = '1'";
$result = mysqli_query($db, $sql_mc);

# Ga op zoek naar alle personen met een mailadres
$sql = "SELECT * FROM $TableUsers WHERE $UserMail != '' AND $UserStatus = 'actief'";
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_array($result);
do {
	# 3 seconden per persoon moet voldoende zijn
	set_time_limit(3);
	
	# identifier is het id binnen scipio
	$scipioID = $row[$UserID];
	
	$data = getMemberDetails($scipioID); 
	$wijk = $data['wijk'];
	
	# Van elke persoon vraag ik op of die al voorkomt in mijn lokale mailchimp-database.
	# 	dat is iets sneller dan aan mailchimp vragen of die al voorkomt én
	#		ik kan dan werken met het scipio id als identiefier ipv het mailadres (wat MC doet)
		
	$sql_mc = "SELECT * FROM $TableMC WHERE $MCID = $scipioID";
	$result_mc = mysqli_query($db, $sql_mc);
	
	# Komt hij niet voor dan moet hij aan MC worden toegevoegd en aan de juiste wijk worden toegekend	
	if(mysqli_num_rows($result_mc) == 0) {
		mc_subscribe($data['mail'], $data['voornaam'], $data['achternaam']);
		//mc_addinterest($data['mail'], $wijkInterest[$wijk]);
		mc_addtag($data['mail'], 'Wijk '. $wijk);
		
		$sql_mc_insert = "INSERT INTO $TableMC ($MCID, $MCmail, $MCfname, $MClname, $MCwijk) VALUES ($scipioID, '". $data['mail'] ."', '". $data['voornaam'] ."', '". $data['achternaam'] ."', '$wijk')";
		mysqli_query($db, $sql_mc_insert);
		
	# Komt hij wel voor dan check ik even of een aantal velden verwijderd zijn :
	#		mailadres
	#		voornaam (en pas dan zowel voor als achternaam aan)
	#		wijk
	} else {
		$row = mysqli_fetch_array($result_mc);
		$sql_update = array();
		$sql_update[] = "$MCmark = '0'";
		
		mc_addtag($data['mail'], 'Wijk '. $wijk);
				
		if($row[$MCmail] != $data['mail']) {
			mc_changemail($row[$MCmail], $data['mail']);
			$sql_update[] = "$MCmail = '". $data['mail'] ."'";
		}
		
		if($row[$MCfname] != $data['voornaam']) {
			mc_changename($row[$MCmail], $data['voornaam'], $data['achternaam']);
			$sql_update[] = "$MCfname = '". $data['voornaam'] ."'";
			$sql_update[] = "$MClname = '". $data['achternaam'] ."'";
		}
		
		if($row[$MCwijk] != $wijk) {
			$oudeWijk = $row[$MCwijk];
			//mc_rminterest($data['mail'], $wijkInterest[$oudeWijk]);
			//mc_addinterest($data['mail'], $wijkInterest[$wijk]);
			
			mc_rmtag($data['mail'], 'Wijk '. $oudeWijk);
			mc_addtag($data['mail'], 'Wijk '. $wijk);
			
			$sql_update[] = "$MCwijk = '$wijk'";
		}
		
		# De wijzigingen aan de MC kant moeten ook verwerkt worden in mijn lokale mailchimp-database
		$sql_mc_update = "UPDATE $TableMC SET ". implode(', ', $sql_update)." WHERE $MCID like $scipioID";
		echo '<b>'. $sql_mc_update .'</b><br>';
		mysqli_query($db, $sql_mc_update);
	}
} while($row = mysqli_fetch_array($result));


$sql_mc_unsub = "SELECT * FROM $TableMC WHERE $MCmark = '1'";
$result_unsub = mysqli_query($db, $sql_mc_unsub);
if($row_unsub = mysqli_fetch_array($result_unsub)) {
	do {
		set_time_limit(3);
		mc_unsubscribe($row_unsub[$MCmail]);
				
	} while($row_unsub = mysqli_fetch_array($result_unsub));
	
	mysqli_query($db, "DELETE FROM $TableMC WHERE $MCmark = '1'");
}

