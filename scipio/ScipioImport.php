<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$test = false;

$db = connect_db();

$client = new SoapClient("ScipioConnect.wsdl");

if(!$test) {
	$object = $client->__soapCall("GetLedenOverzicht", array($scipioParams));
	$temp =  (array) $object;
	$xmlfile = $temp['GetLedenOverzichtResult'];
} else {
	$xmlfile = file_get_contents('dump.txt');
}

$xml = new SimpleXMLElement(utf8_encode($xmlfile));

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
		$update = array();
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
?>