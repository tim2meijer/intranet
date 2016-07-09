<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');

$db = connect_db();

if(!isset($_REQUEST['rooster'])) {
	echo "geen rooster gedefinieerd";
	exit;
}

# Zoek op wie de beheerder is
$beheerder = getBeheerder4Rooster($_REQUEST['rooster']);

# Ken kijk-rechten
$requiredUserGroups = array(1, $beheerder);
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");

# Eerste keer data ophalen voor in logfiles enzo
$RoosterData = getRoosterDetails($_REQUEST['rooster']);

# Als op de knop van de mail geklikt is die data wegschrijven
if(isset($_POST['save_mail'])) {
	$sql = "UPDATE $TableRoosters SET $RoostersMail = '". urlencode($_POST['text_mail']) ."', $RoostersSubject = '". urlencode($_POST['onderwerp_mail']) ."', $RoostersFrom = '". urlencode($_POST['naam_afzender']) ."',	$RoostersFromAddr = '". urlencode($_POST['mail_afzender']) ."' WHERE $RoostersID = ". $_POST['rooster'];
	mysqli_query($db, $sql);
	toLog('info', $_SESSION['ID'], '', 'Mail voor '. $RoosterData['naam'] .' aangepast');
}

# Als er op een knop gedrukt is, het rooster wegschrijven
if(isset($_POST['save']) OR isset($_POST['maanden'])) {
	foreach($_POST['persoon'] as $dienst => $personen) {
		# Alle gegevens voor de dienst verwijderen
		removeFromRooster($_POST['rooster'], $dienst);
		
		# En de nieuwe wegschrijven
		foreach($personen as $pos => $persoon) {
			if($persoon != '' AND $persoon != 0) {
				add2Rooster($_POST['rooster'], $dienst, $persoon, $pos);
			}
		}		
	}
	toLog('info', $_SESSION['ID'], '', 'Rooster '. $RoosterData['naam'] .' aangepast');
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

# Roosterdata voor de 2de keer opvragen (hierboven kan de data gewijzigd zijn)
# Nu gelijk ook maar groepsdata opvragen
$RoosterData = getRoosterDetails($_REQUEST['rooster']);
$IDs = getGroupMembers($RoosterData['groep']);

# Als er geen groep is, gewoon de hele gemeente nemen
if(count($IDs) == 0) {
	$IDs = getMembers('volwassen');
	$type = 13;
} else {
	$type = 5;
}

# Doorloop de hele groep en haal hun namen op
foreach($IDs as $member) {
	$namen[$member] = makeName($member, $type);
}	

# Haal alle kerkdiensten binnen een tijdsvak op
$diensten = getKerkdiensten(time(), mktime(date("H"),date("i"),date("s"),(date("n")+(3*$blokken)),date("j"),date("Y")));
$nrFields = $RoosterData['aantal'];

$block_1[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
$block_1[] = "<input type='hidden' name='rooster' value='". $_REQUEST['rooster'] ."'>";
$block_1[] = "<input type='hidden' name='blokken' value='$blokken'>";
$block_1[] = "<table>";

foreach($diensten as $dienst) {
	$details = getKerkdienstDetails($dienst);
	$vulling = getRoosterVulling($_REQUEST['rooster'], $dienst);
	$selected = current($vulling);
	
	$block_1[] = "<tr>";
	$block_1[] = "	<td align='right'>". strftime("%A %d %b %H:%M", $details['start'])."</td>";
	
	for($n=0 ; $n < $nrFields ; $n++) {
		$block_1[] = "	<td><select name='persoon[$dienst][]'>";
		$block_1[] = "	<option value=''>&nbsp;</option>";
						
		foreach($namen as $key => $naam) {
			$block_1[] = "	<option value='$key'". ($selected == $key ? " selected" : '') .">$naam</option>";
		}		
		
		$block_1[] = "</select></td>";
		$selected = next($vulling);
	}	
	$block_1[] = "</tr>";
}

$block_1[] = "<tr>";
$block_1[] = "<td colspan='". ($nrFields+1) ."' align='middle'><input type='submit' name='save' value='Rooster opslaan'>&nbsp;<input type='submit' name='maanden' value='Volgende 3 maanden'></td>";
$block_1[] = "</tr>";
$block_1[] = "</table>";
$block_1[] = "</form>";


$block_2[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
$block_2[] = "<input type='hidden' name='rooster' value='". $_REQUEST['rooster'] ."'>";
$block_2[] = "<table>";
$block_2[] = "<tr>";
$block_2[] = "	<td valign='top'>Afzendernaam</td>";
$block_2[] = "	<td valign='top' colspan='2'><input type='text' name='naam_afzender' size=80 value='".$RoosterData['naam_afzender'] ."'></td>";
$block_2[] = "</tr>";
$block_2[] = "<tr>";
$block_2[] = "	<td valign='top'>Mailadres</td>";
$block_2[] = "	<td valign='top' colspan='2'><input type='text' name='mail_afzender' size=80 value='".$RoosterData['mail_afzender'] ."'></td>";
$block_2[] = "</tr>";
$block_2[] = "<tr>";
$block_2[] = "	<td valign='top'>Onderwerp</td>";
$block_2[] = "	<td valign='top' colspan='2'><input type='text' name='onderwerp_mail' size=80 value='".$RoosterData['onderwerp_mail'] ."'></td>";
$block_2[] = "</tr>";
$block_2[] = "<tr>";
$block_2[] = "	<td valign='top'>Mailtekst</td>";
$block_2[] = "	<td valign='top'><textarea name='text_mail' rows=20 cols=60>". $RoosterData['text_mail'] ."</textarea></td>";
$block_2[] = "	<td valign='top'>[[voornaam]] = voornaam van de ontvanger<br>";
$block_2[] = "	[[team]] = namen van iedereen die voor die dag op het rooster staat<br>";
$block_2[] = "	[[dag]] = naam van de dag. Meestal zondag, bij feestdagen meestal andere dag.</td>";
$block_2[] = "</tr>";
$block_2[] = "<tr>";
$block_2[] = "	<td valign='top'>&nbsp;</td><td valign='top' colspan='2'><input type='submit' name='save_mail' value='Mail-gegevens opslaan'></td>";
$block_2[] = "</tr>";
$block_2[] = "</table>";
$block_2[] = "</form>";

echo $HTMLHeader;
echo "<h1>". $RoosterData['naam'] ."</h1>".NL;
echo showBlock(implode(NL, $block_1), 100);
echo "<p>";
echo showBlock(implode(NL, $block_2), 100);
echo $HTMLFooter;
?>