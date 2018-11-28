<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');

# ALTER TABLE `kerkdiensten` CHANGE `voorganger` `voorganger` INT NOT NULL;

$requiredUserGroups = array(1, 20);
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");

# Als er op een knop gedrukt is, het rooster wegschrijven
if(isset($_POST['save']) OR isset($_POST['maanden'])) {	
	foreach($_POST['voorganger'] as $dienst => $voorgangerID) {
		if($voorgangerID > 1) {
			$sql = "UPDATE $TableDiensten SET $DienstVoorganger = $voorgangerID WHERE $DienstID = ". $dienst;		
			
			if(!mysql_query($sql)) {
				$text[] = "Ging iets niet goed met geegevens opslaan";
				//toLog('error', $_SESSION['ID'], '', 'Gegevens voorganger ('. $_REQUEST['voorgangerID'] .') konden niet worden opgeslagen');
			}
		}
	}
	toLog('info', $_SESSION['ID'], '', 'Diensten bijgewerkt');
}

# Als er op de knop van 3 maanden extra geklikt is, 3 maanden bij de eindtijd toevoegen
# Eerst initeren, event. later ophogen
if(isset($_POST['blokken'])) {
	$blokken = $_POST['blokken'];
} else {
	$blokken = 1;
}

if(isset($_POST['maanden'])) {
	$blokken++;
}

# Haal alle kerkdiensten binnen een tijdsvak op
$diensten = getKerkdiensten(mktime(0,0,0), mktime(date("H"),date("i"),date("s"),(date("n")+(3*$blokken))));

# Haal alle voorgangers op en maak een namen-array
$voorgangers = getVoorgangers();

foreach($voorgangers as $voorgangerID) {
	$voorgangerData = getVoorgangerData($voorgangerID);
	$voor = ($voorgangerData['voor'] == '' ? $voorgangerData['init'] : $voorgangerData['voor']);
	//$voorgangersNamen[$voorgangerID] = $voor.' '.($voorgangerData['tussen'] == '' ? '' : $voorgangerData['tussen']. ' ').$voorgangerData['achter'];
	$voorgangersNamen[$voorgangerID] = $voorgangerData['achter'].', '.$voor.($voorgangerData['tussen'] == '' ? '' : '; '.$voorgangerData['tussen']);
}

# Bouw formulier op
$text[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
$text[] = "<input type='hidden' name='blokken' value='$blokken'>";
$text[] = "<table>";
$text[] = "<tr>";
$text[] = "	<td>Datum</td>";
$text[] = "	<td>Start</td>";
$text[] = "	<td>Voorganger</td>";
$text[] = "	<td>Bijzonderheid</td>";
$text[] = "</tr>";

foreach($diensten as $dienst) {
	$data = getKerkdienstDetails($dienst);
	
	$text[] = "<tr>";
	//$text[] = "	<td align='right'>". strftime("%a %e %b", $data['start']) ."</td>";
	$text[] = "	<td align='right'>". date("d-m-Y", $data['start']) ."</td>";
	$text[] = "	<td>". date('H:i', $data['start']) ."</td>";
	$text[] = "	<td>";
	$text[] = "<select name='voorganger[$dienst]'>";
	$text[] = "	<option value=''></option>";
	
	foreach($voorgangersNamen as $voorgangerID => $naam) {
		$text[] = "	<option value='$voorgangerID'". ($data['voorganger'] == $voorgangerID ? ' selected' : '') .">$naam</option>";
	}
		
	$text[] = "</select>";
	$text[] = "	</td>";
	$text[] = "	<td>". $data['bijzonderheden'] ."</td>";
	$text[] = "</tr>";
}

$text[] = "<tr>";
$text[] = "	<td colspan='4' align='middle'><input type='submit' name='save' value='Diensten opslaan'>&nbsp;<input type='submit' name='maanden' value='Volgende 3 maanden'></td>";
$text[] = "</tr>";
$text[] = "</table>";
$text[] = "</form>";

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>