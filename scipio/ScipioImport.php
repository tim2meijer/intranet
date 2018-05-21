<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$test = false;
$debug = false;

$db = connect_db();

$client = new SoapClient("ScipioConnect.wsdl");

if(!$test) {
	$object = $client->__soapCall("GetLedenOverzicht", array($scipioParams));
	$temp =  (array) $object;
	$xmlfile = $temp['GetLedenOverzichtResult'];
	if($debug) {
		$file = fopen('dump.txt', 'w+');
		fwrite($file, $xmlfile);
		fclose($file);
	}
} else {
	$xmlfile = file_get_contents('dump.txt');
}

$xml = new SimpleXMLElement($xmlfile);

foreach ($xml->persoon as $element) {
	$namen = explode(' - ', $element->aanschrijfnaam);
	
	if(count($namen) == 2) {
		$velden[$UserMeisjesnaam] = trim($namen[1]);
	} else {
		$velden[$UserMeisjesnaam] = "";
	}
	
	$delen = explode(' ', $namen[0]);
	
	$velden[$UserVoorletters] = array_shift($delen);
	$velden[$UserAchternaam] = array_pop($delen);
	$velden[$UserTussenvoegsel] = implode(' ', $delen);		
	$velden[$UserAdres] = $element->pefamilie;
	$velden[$UserID] = $element->regnr;
	//$velden[] = 'aanschrijfnaam';
	$velden[$UserVoornaam] = $element->roepnaam;
	$velden[$UserGeslacht] = $element->geslacht;
	$velden[$UserGeboorte] = substr($element->gebdatum, 0, 4).'-'.substr($element->gebdatum, 4, 2).'-'.substr($element->gebdatum, 6, 2);
	$velden[$UserStatus] = $element->status;
	$velden[$UserBurgelijk] = $element->burgstaat;
	$velden[$UserBelijdenis] = $element->kerkstaat;
	$velden[$UserRelatie] = $element->gezinsrelatie;
	$velden[$UserMail] = $element->email;
	$velden[$UserStraat] = $element->straat;
	$velden[$UserHuisnummer] = $element->huisnr;
	//$velden[] = 'huisltr';
	$velden[$UserToevoeging] = $element->huisnrtoev;
	$velden[$UserPC] = $element->postcode;
	$velden[$UserPlaats] = $element->plaats;
	//$velden[] = 'vestigingsdatum';
	$velden[$UserWijk] = substr($element->wijk, -1);
	//$velden[] = 'sectie';
	//$velden[] = 'mutatiedatum';
	$velden[$UserTelefoon] = $element->telnr;
	
	if($velden[$UserVoorletters] == $velden[$UserVoornaam]) {
		$delen = explode(' ', $velden[$UserVoornaam]);
		$velden[$UserVoorletters] = '';
		
		foreach($delen as $naam) {
			$velden[$UserVoorletters] .= $naam[0].'.';
		}
	}
	
	# Even alle velden doorlopen om slashes toe te voegen
	foreach($velden as $key => $value) {
		$velden[$key] = addslashes($value);
	}
	
	# Komt het lid al voor ?
	$sql_check = "SELECT $UserID FROM $TableUsers WHERE $UserID like '". $element->regnr ."'";
	$result = mysqli_query($db, $sql_check);
	
	# Nee -> Toevoegen
	if(mysqli_num_rows($result) == 0) {		
		$sql_insert = "INSERT INTO $TableUsers (". implode(', ', array_keys($velden)) .") VALUES ('". implode("', '", array_values($velden)) ."')";
		if(!mysqli_query($db, $sql_insert)) {
			 echo '<b>'. $sql_insert ."</b><br>\n";
			 toLog('error', '', $element->regnr, 'Toevoegen mislukt');
		} else {
			echo makeName($element->regnr, 5). " toegevoegd<br>\n";
			toLog('info', '', $element->regnr, 'Toegevoegd');
		}
		
	# Ja -> updaten
	} else {
		$oldData = getMemberDetails($element->regnr);		
		if($oldData['status'] != $velden[$UserStatus])												toLog('info', '', $element->regnr, 'Wijziging Scipio status: '. $oldData['status'] .' -> '. $velden[$UserStatus]);
		if($oldData['adres'] != $velden[$UserAdres])													toLog('info', '', $element->regnr, 'Wijziging Scipio adres: '. $oldData['adres'] .' -> '. $velden[$UserAdres]);
		if($oldData['belijdenis'] != $velden[$UserBelijdenis])								toLog('info', '', $element->regnr, 'Wijziging Scipio belijdenis: '. $oldData['belijdenis'] .' -> '. $velden[$UserBelijdenis]);
		if(addslashes($oldData['achternaam']) != $velden[$UserAchternaam])		toLog('info', '', $element->regnr, 'Wijziging Scipio achternaam: '. $oldData['achternaam'] .' -> '. $velden[$UserAchternaam]);
		if(addslashes($oldData['meisjesnaam']) != $velden[$UserMeisjesnaam])	toLog('info', '', $element->regnr, 'Wijziging Scipio meisjesnaam: '. $oldData['meisjesnaam'] .' -> '. $velden[$UserMeisjesnaam]);
		if(addslashes($oldData['straat']) != $velden[$UserStraat])						toLog('info', '', $element->regnr, 'Wijziging Scipio straat: '. $oldData['straat'] .' -> '. $velden[$UserStraat]);
		if($oldData['huisnummer'] != $velden[$UserHuisnummer])								toLog('info', '', $element->regnr, 'Wijziging Scipio huisnummer: '. $oldData['huisnummer'] .' -> '. $velden[$UserHuisnummer]);
		if($oldData['toevoeging'] != $velden[$UserToevoeging])								toLog('info', '', $element->regnr, 'Wijziging Scipio toevoeging: '. $oldData['toevoeging'] .' -> '. $velden[$UserToevoeging]);
		if($oldData['burgelijk'] != $velden[$UserBurgelijk])									toLog('info', '', $element->regnr, 'Wijziging Scipio burgerlijk: '. $oldData['burgerlijk'] .' -> '. $velden[$UserBurgelijk]);
		if($oldData['relatie'] != $velden[$UserRelatie])											toLog('info', '', $element->regnr, 'Wijziging Scipio relatie: '. $oldData['relatie'] .' -> '. $velden[$UserRelatie]);
		if($oldData['tel'] != $velden[$UserTelefoon])													toLog('info', '', $element->regnr, 'Wijziging Scipio telefoon: '. $oldData['tel'] .' -> '. $velden[$UserTelefoon]);
		if($oldData['mail'] != $velden[$UserMail])														toLog('info', '', $element->regnr, 'Wijziging Scipio mail: '. $oldData['mail'] .' -> '. $velden[$UserMail]);
		
		$update = array();
		$update[] = "$UserLastChange = ". time();
		foreach($velden as $veld => $waarde) {
			$update[] = "$veld = '$waarde'";
		}
					
		$sql_update = "UPDATE $TableUsers SET ". implode(', ', $update) ." WHERE $UserID like '". $element->regnr ."'";
		if(!mysqli_query($db, $sql_update)) {
			 echo '<b>'. $sql_update ."</b><br>\n";
			 toLog('error', '', $element->regnr, 'Updaten mislukt');
		}
	}
}

toLog('info', '', '', 'Scipio data ingeladen');
?>