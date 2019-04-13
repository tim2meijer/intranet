<?php
include_once('../include/functions.php');
include_once('../include/MC_functions.php');
include_once('../include/config.php');

$db = connect_db();

# Ga op zoek naar alle personen met een mailadres
# Mailadres is daarbij alles met een @-teken erin
$sql = "SELECT * FROM $TableUsers WHERE $UserMail like '%@%' AND $UserStatus = 'actief'";
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_array($result);
do {
	# 3 seconden per persoon moet voldoende zijn
	set_time_limit(3);
	
	# identifier is het id binnen scipio
	$scipioID = $row[$UserID];
	
	# Haal alle gegevens op
	$data = getMemberDetails($scipioID); 
	$wijk = $data['wijk'];
	
	# Van elke persoon vraag ik op of die al voorkomt in mijn lokale mailchimp-database.
	# 	dat is iets sneller dan aan mailchimp vragen of die al voorkomt én
	#		ik kan dan werken met het scipio id als identiefier ipv het mailadres (wat MC doet)		
	$sql_mc = "SELECT * FROM $TableMC WHERE $MCID = $scipioID";
	$result_mc = mysqli_query($db, $sql_mc);
	
	# Komt hij niet voor dan moet hij aan MC worden toegevoegd en aan de juiste wijk worden toegekend	
	if(mysqli_num_rows($result_mc) == 0) {
		# Toevoegen aan de database
		if(mc_subscribe($data['mail'], $data['voornaam'], $data['tussenvoegsel'], $data['achternaam'])) {
			toLog('info', '', $scipioID, 'Toegevoegd in MailChimp [S]');
			echo makeName($scipioID, 6) ." toegevoegd<br>\n";
		} else {
			toLog('error', '', $scipioID, 'Kon niet toevoegen in MailChimp [S]');
		}
		
		# + tag van de juiste wijk eraan
		if(mc_addtag($data['mail'], $tagWijk[$wijk])) {
			toLog('debug', '', $scipioID, 'Wijk toegekend in MailChimp [S]');
		} else {
			toLog('error', '', $scipioID, 'Kon geen wijk toekennen in MailChimp [S]');
		}
		
		# + tag dat deze vanuit Scipio komt
		if(mc_addtag($data['mail'], $tagScipio)) {
			toLog('debug', '', $scipioID, 'Scipio-tag toegekend in MailChimp [S]');
		} else {
			toLog('error', '', $scipioID, 'Kon geen Scipio-tag toekennen in MailChimp [S]');
		}
		
		# + Scipio-ID toevoegen
		if(mc_addSipioID($email, $scipioID)) {
			toLog('debug', '', $scipioID, 'ScipioID toegevoegd in MailChimp [S]');
		} else {
			toLog('error', '', $scipioID, 'Kon geen ScipioID toevoegen in MailChimp [S]');
		}
		
		# + toevoegen aan GoogleGroups
		if(mc_addinterest($data['mail'], $ID_google)) {
			toLog('debug', '', $scipioID, 'Toegevoegd aan GoogleGroups in MailChimp [S]');
		} else {
			toLog('error', '', $scipioID, 'Kon niet toevoegen aan GoogleGroups in MailChimp [S]');
		}
				
		
		# De wijzigingen aan de MC kant moeten ook verwerkt worden in mijn lokale mailchimp-database
		$sql_mc_insert = "INSERT INTO $TableMC ($MCID, $MCmail, $MCfname, $MCtname, $MClname, $MCwijk, $MCstatus, $MClastChecked, $MClastSeen) VALUES ($scipioID, '". $data['mail'] ."', '". $data['voornaam'] ."', '". urlencode($data['tussenvoegsel']) ."', '". $data['achternaam'] ."', '$wijk', 'subscribe', ". time() .", ". time() .")";
		if(mysqli_query($db, $sql_mc_insert)) {
			toLog('debug', '', $scipioID, 'Mailchimp-data toegevoegd in lokale MC-tabel [S]');
		} else {
			toLog('error', '', $scipioID, 'Kon niet toevoegen in lokale MC-tabel [S]');
		}
		
				
		
	# Komt hij wel voor dan check ik even of een aantal velden gewijzigd zijn :
	#		mailadres / naam / wijk
	} else {
		$row_mc = mysqli_fetch_array($result_mc);
		$email = $row_mc[$MCmail];
		
		$sql_update = array();
		$sql_update[] = "$MClastSeen = ". time();
		
		
		# Stond in de tabel als niet ingeschreven
		if($row_mc[$MCstatus] == 'unsubscribe') {
			if(mc_resubscribe($email)) {
				toLog('info', '', $scipioID, 'Opnieuw ingeschreven in MailChimp [S]');
				$sql_update[] = "$MClastSeen = 'subscribe'";
			} else {
				toLog('error', '', $scipioID, 'Kon niet opnieuw inschrijven in MailChimp [S]');
			}
		}
		
												
		# Gewijzigd mailadres
		if($email != $data['mail']) {
			if(mc_changemail($email, $data['mail'])) {
				toLog('info', '', $scipioID, 'Mailadres gewijzigd in MailChimp [S]');
				$sql_update[] = "$MCmail = '". $data['mail'] ."'";
			} else {
				toLog('error', '', $scipioID, 'Kon mailadres niet wijzigen in MailChimp [S]');
			}			
		}
		
		# Gewijzigde naam
		if($row_mc[$MCfname] != $data['voornaam'] OR urldecode($row_mc[$MCtname]) != $data['tussenvoegsel'] OR $row_mc[$MClname] != $data['achternaam']) {
			if(mc_changename($email, $data['voornaam'], $data['tussenvoegsel'], $data['achternaam'])) {
				toLog('info', '', $scipioID, 'Naam gewijzigd in MailChimp [S]');
				$sql_update[] = "$MCfname = '". $data['voornaam'] ."'";
				$sql_update[] = "$MCtname = '". urlencode($data['tussenvoegsel']) ."'";
				$sql_update[] = "$MClname = '". $data['achternaam'] ."'";
			} else {
				toLog('error', '', $scipioID, 'Kon naam niet wijzigen in MailChimp [S]');
			}
		}
		
		# Gewijzigde wijk
		if($row_mc[$MCwijk] != $wijk) {
			$oudeWijk = $row_mc[$MCwijk];			
			if(mc_addtag($email, $tagWijk[$wijk]) AND mc_rmtag($email, $tagWijk[$oudeWijk])){
				toLog('info', '', $scipioID, "Wijk gewijzigd van wijk $oudeWijk naar wijk $wijk [S]");
				$sql_update[] = "$MCwijk = '$wijk'";
			} else {
				toLog('error', '', $scipioID, "Kon wijk niet gewijzigen van wijk $oudeWijk naar wijk $wijk [S]");
			}
		}
		
		# De wijzigingen aan de MC kant moeten ook verwerkt worden in mijn lokale mailchimp-database
		$sql_mc_update = "UPDATE $TableMC SET ". implode(', ', $sql_update)." WHERE $MCID like $scipioID";
		mysqli_query($db, $sql_mc_update);		
		toLog('debug', '', $scipioID, 'Gesynced met MailChimp');
	}
} while($row = mysqli_fetch_array($result));



# Verwijder adressen die al sinds eergisteren niet meer gezien zijn
$dagen = mktime (0, 0, 0, date("n"), (date("j")-2));
$sql_mc_unsub = "SELECT * FROM $TableMC WHERE $MCstatus like 'subscribe' AND $MClastSeen < ". $dagen;
$result_unsub = mysqli_query($db, $sql_mc_unsub);
if($row_unsub = mysqli_fetch_array($result_unsub)) {
	do {
		set_time_limit(3);
		mc_unsubscribe($row_unsub[$MCmail]);
		toLog('info', '', $row_unsub[$MCID], 'Uitgeschreven in MailChimp [S]');
		mysqli_query($db, "UPDATE $TableMC SET $MCstatus = 'unsubscribe' WHERE $MCID = ". $row_unsub[$MCID]);				
	} while($row_unsub = mysqli_fetch_array($result_unsub));
}

?>
