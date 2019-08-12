<?php
include_once('../include/functions.php');
include_once('../include/MC_functions.php');
include_once('../include/config.php');

$db = connect_db();

# Ga op zoek naar de persoon die het langst niet gecontroleerd is
$sql = "SELECT * FROM $TableMC ORDER BY $MClastChecked ASC LIMIT 0,25";
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_array($result);
do {
	# 3 seconden per persoon moet voldoende zijn
	set_time_limit(3);
	
	# variabelen definieren vanuit de lokale tabel
	$scipioID		= $row[$MCID];
	$voor				= $row[$MCfname];
	$tussen			= urldecode($row[$MCtname]);
	$achter			= $row[$MClname];
	$wijk				= $row[$MCwijk];	
	$email			= $row[$MCmail];
	$status			= $row[$MCstatus];	
	$relatie		= $row[$MCrelatie];
	$kerkStatus	= $row[$MCdoop];
	
	# op basis van deze lokale data config-variabelen definieren
	$segment_id = $tagWijk[$wijk];
	$relatie_id = $tagRelatie[$relatie];
	$status_id	= $tagStatus[$kerkStatus];
			
	# variabelen definieren vanuit de MC-data
	$data = mc_getData($email);
	$tags	= $data['tags'];
		
	# Staat adres wel aan beide kanten als ingeschreven
	if($status == 'subscribe' AND $data['status'] != 'subscribed') {
		if(mc_resubscribe($email)) {
			toLog('info', '', $scipioID, 'Opnieuw ingeschreven na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon niet opnieuw inschrijven na controle in MailChimp');
		}
	}
	
	# Staat adres wel aan beide kanten als niet-ingeschreven
	if($status == 'unsubscribe' AND $data['status'] != 'unsubscribed') {
		if(mc_unsubscribe($email)) {
			toLog('info', '', $scipioID, 'Opnieuw uitgeschreven na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon niet opnieuw uitschrijven na controle in MailChimp');
		}
	}
	
	# Check of naam wel correct is in MailChimp
	if($data['voornaam'] != $voor OR $data['tussen'] != $tussen OR $data['achter'] != $achter) {
		if(mc_changename($email, $voor, $tussen, $achter)) {
			toLog('info', '', $scipioID, 'Naam opnieuw ingesteld na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon naam niet opnieuw instellen na controle in MailChimp');
		}
	}
	
	# Check of de tag 'Wijk ?' aan dit adres hangt
	if(!array_key_exists($segment_id, $tags) AND $status == 'subscribed') {
		if(mc_addtag($email, $segment_id)) {
			toLog('info', '', $scipioID, 'Wijk opnieuw ingesteld na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon wijk niet opnieuw instellen na controle in MailChimp');
		}
	}
	
	# Check of de tag 'belijdend-lid'/'dooplid' aan dit adres hangt
	if(!array_key_exists($status_id, $tags) AND $status == 'subscribed') {
		if(mc_addtag($email, $status_id)) {
			toLog('info', '', $scipioID, 'Kerkelijke status opnieuw ingesteld na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon kerkelijke status niet opnieuw instellen na controle in MailChimp');
		}
	}
	
	# Check of de tag 'gezinshoofd'/'zoon'/'dochter' etc aan dit adres hangt
	if(!array_key_exists($relatie_id, $tags) AND $status == 'subscribed') {
		if(mc_addtag($email, $relatie_id)) {
			toLog('info', '', $scipioID, 'Kerkelijke relatie opnieuw ingesteld na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon kerkelijke relatie niet opnieuw instellen na controle in MailChimp');
		}
	}
	
	# Check of de tag 'Scipio' aan dit adres hangt
	if(!array_key_exists($tagScipio, $tags) AND $status == 'subscribed') {
		if(mc_addtag($email, $tagScipio)) {
			toLog('info', '', $scipioID, 'Scipio-tag opnieuw ingesteld na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon scipio-tag niet opnieuw instellen na controle in MailChimp');
		}			
	}
	
	# Check of ScipioID wel is ingevuld in MailChimp
	if($data['scipio'] == '' AND $status == 'subscribed') {
		if(mc_addScipioID($email, $scipioID)) {
			toLog('info', '', $scipioID, 'ScipioID toegevoegd na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon scipio-ID niet toevoegen na controle in MailChimp');
		}
	}
	
	# Check of hash wel is ingevuld in MailChimp
	if($data['hash'] == '' AND $status == 'subscribed') {
		$memberData = getMemberDetails($scipioID);
		
		if(mc_addHash($email, $memberData['hash_long'])) {
			toLog('info', '', $scipioID, 'Hash toegevoegd na controle in MailChimp');
		} else {
			toLog('error', '', $scipioID, 'Kon hash niet toevoegen na controle in MailChimp');
		}
	}	
		
	# De wijzigingen aan de MC kant moeten ook verwerkt worden in mijn lokale mailchimp-database
	$sql_update = "UPDATE $TableMC SET $MClastChecked = ". time() . " WHERE $MCID like $scipioID";
	mysqli_query($db, $sql_update);	
} while($row = mysqli_fetch_array($result));

toLog('info', '', '', 'Controle MailChimp-data uitgevoerd');

?>
