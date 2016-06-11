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

# Als er op een knop gedrukt is, het rooster wegschrijven
if(isset($_POST['save']) OR isset($_POST['maanden'])) {
	foreach($_POST['persoon'] as $dienst => $personen) {
		# Alle gegevens voor de dienst verwijderen
		removeFromRooster($_POST['rooster'], $dienst);
		
		# En de nieuwe wegschrijven
		foreach($personen as $persoon) {
			if($persoon != '' OR $persoon != 0) {
				add2Rooster($_POST['rooster'], $dienst, $persoon);
			}
		}
	}
}

# Als op de knop van de mail geklikt is die data wegschrijven
if(isset($_POST['save_mail'])) {
	$sql = "UPDATE $TableRoosters SET $RoostersMail = '". urlencode($_POST['text_mail']) ."', $RoostersSubject = '". urlencode($_POST['onderwerp_mail']) ."' WHERE $RoostersID = ". $_POST['rooster'];
	mysqli_query($db, $sql);
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

# Roosterdata en groepsdata opvragen
$RoosterData = getRoosterDetails($_REQUEST['rooster']);
$IDs = getGroupMembers($RoosterData['groep']);

# Als er geen groep is, gewoon de hele gemeente nemen
if(count($IDs) == 0) {
	$IDs = getMembers('adressen');
}

# Doorloop de hele groep en haal hun namen op
foreach($IDs as $member) {
	$namen[$member] = makeName($member, 5);
}	

# Haal alle kerkdiensten binnen een tijdsvak op
$diensten = getKerkdiensten(time(), mktime(date("H"),date("i"),date("s"),(date("n")+(3*$blokken)),date("j"),date("Y")));
$nrFields = 3;//getMaxFields($_REQUEST['id']);

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
		$block_1[] = "<option value=''>&nbsp;</option>";
						
		foreach($namen as $key => $naam) {
			$block_1[] = "<option value='$key'". ($selected == $key ? " selected" : '') .">$naam</option>";
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
$block_2[] = "	<td valign='top'>Onderwerp</td>";
$block_2[] = "	<td valign='top' colspan='2'><input type='text' name='onderwerp_mail' size=70 value='".$RoosterData['onderwerp_mail'] ."'></td>";
$block_2[] = "</tr>";
$block_2[] = "<tr>";
$block_2[] = "	<td valign='top'>Mailtekst</td>";
$block_2[] = "	<td valign='top'><textarea name='text_mail' rows=30 cols=60>". $RoosterData['text_mail'] ."</textarea></td>";
$block_2[] = "	<td valign='top'>[[voornaam]] = voornaam van de ontvanger<br>[[team]] = namen van iedereen die voor die zondag op het rooster staat</td>";
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