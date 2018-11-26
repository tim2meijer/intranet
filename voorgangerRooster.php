<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');

$requiredUserGroups = array(1, 20);
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");

# Als er op een knop gedrukt is, het rooster wegschrijven
if(isset($_POST['save']) OR isset($_POST['maanden'])) {	
	foreach($_POST['sDag'] as $dienst => $dummy) {
		$startTijd = mktime($_POST['sUur'][$dienst], $_POST['sMin'][$dienst], 0, $_POST['sMaand'][$dienst], $_POST['sDag'][$dienst], $_POST['sJaar'][$dienst]);
		$eindTijd = mktime($_POST['eUur'][$dienst], $_POST['eMin'][$dienst], 0, $_POST['sMaand'][$dienst], $_POST['sDag'][$dienst], $_POST['sJaar'][$dienst]);
		
		$set = array();
		
		if(in_array(1, getMyGroups($_SESSION['ID']))) {
			$set[] = $DienstStart .' = '. $startTijd;
			$set[] = $DienstEind .' = '. $eindTijd;
		}
				
		if(in_array(1, getMyGroups($_SESSION['ID'])) OR in_array(22, getMyGroups($_SESSION['ID']))) {
			$set[] = $DienstCollecte_1 .' = \''. urlencode($_POST['collecte_1'][$dienst]) .'\'';
			$set[] = $DienstCollecte_2 .' = \''. urlencode($_POST['collecte_2'][$dienst]) .'\'';
		}
		
		if(in_array(1, getMyGroups($_SESSION['ID'])) OR in_array(20, getMyGroups($_SESSION['ID']))) {
			$set[] = $DienstVoorganger .' = \''. urlencode($_POST['voorganger'][$dienst]) .'\'';
			$set[] = $DienstOpmerking .' = \''. urlencode($_POST['bijz'][$dienst]) .'\'';
		}
		
		$sql = "UPDATE $TableDiensten SET ". implode(', ', $set)." WHERE $DienstID = ". $dienst;		
		mysql_query($sql);
	}
	toLog('info', $_SESSION['ID'], '', 'Diensten bijgewerkt');
}

if(isset($_REQUEST['new'])) {
	$start	= mktime(10,0,0,date("n"),date("j"), date("Y"));
	$eind		= mktime(11,30,0,date("n"),date("j"), date("Y"));		
	$query	= "INSERT INTO $TableDiensten ($DienstStart, $DienstEind) VALUES ('$start', '$eind')";
	$result = mysqli_query($db, $query);
		
	$id		= mysqli_insert_id($db);
	
	toLog('info', $_SESSION['ID'], '', 'Dienst van '. date("d-m-Y", $start) .' toegevoegd');
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
	$text[] = "	<td align='right'>". strftime("%a %e %b", $data['start']) ."</td>";
	$text[] = "	<td>". date('H:i', $data['start']) ."</td>";
	$text[] = "	<td>";
	$text[] = "<select name='voorganger[$dienst]'>";
	$text[] = "	<option value=''></option>";
	
	foreach($voorgangersNamen as $voorgangerID => $naam) {
		$text[] = "	<option value='$voorgangerID'>$naam</option>";
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