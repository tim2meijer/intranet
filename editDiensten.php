<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');

$requiredUserGroups = array(1, 28);
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

# Als er op een knop gedrukt is, het rooster wegschrijven
if(isset($_POST['save']) OR isset($_POST['maanden'])) {	
	foreach($_POST['bijz'] as $dienst => $bijzonderheid) {
		$details	= getKerkdienstDetails($dienst);
		$dag				= date("d", $details['start']);
		$maand			= date("m", $details['start']);
		$jaar				= date("Y", $details['start']);

		$startTijd	= mktime($_POST['sUur'][$dienst], $_POST['sMin'][$dienst], 0, $maand, $dag, $jaar);
		$eindTijd		= mktime($_POST['eUur'][$dienst], $_POST['eMin'][$dienst], 0, $maand, $dag, $jaar);
		
		$set = array();
		
		$set[] = $DienstStart .' = '. $startTijd;
		$set[] = $DienstEind .' = '. $eindTijd;
		$set[] = $DienstOpmerking .' = \''. urlencode($bijzonderheid) .'\'';
				
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

$text[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
$text[] = "<input type='hidden' name='blokken' value='$blokken'>";
$text[] = "<table>";
$text[] = "<tr>";
$text[] = "	<td>Datum</td>";
$text[] = "	<td>Start</td>";
$text[] = "	<td>Eind</td>";
$text[] = "	<td>Bijzonderheid</td>";
$text[] = "</tr>";

foreach($diensten as $dienst) {
	$data = getKerkdienstDetails($dienst);
	
	$sMin		= date("i", $data['start']);
	$sUur		= date("H", $data['start']);
	
	$eMin		= date("i", $data['eind']);
	$eUur		= date("H", $data['eind']);
	
	$text[] = "<tr>";
	$text[] = "	<td align='right'>". strftime("%a %e %b", $data['start']) ."</td>";
	$text[] = "	<td><select name='sUur[$dienst]'>";
	for($u=0; $u<24 ; $u++) {
		$text[] = "	<option value='$u'". ($u == $sUur ? ' selected' : '') .">$u</option>";
	}
	$text[] = "	</select>";
	$text[] = "	<select name='sMin[$dienst]'>";
	for($m=0; $m<60 ; $m=$m+15) {
		$text[] = "	<option value='$m'". ($m == $sMin ? ' selected' : '') .">". substr('0'.$m, -2) ."</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "	<td><select name='eUur[$dienst]'>";
	for($u=0; $u<24 ; $u++) {
		$text[] = "	<option value='$u'". ($u == $eUur ? ' selected' : '') .">$u</option>";
	}
	$text[] = "	</select>";
	$text[] = "	<select name='eMin[$dienst]'>";
	for($m=0; $m<60 ; $m=$m+15) {
		$text[] = "	<option value='$m'". ($m == $eMin ? ' selected' : '') .">". substr('0'.$m, -2) ."</option>";
	}
	$text[] = "	</select></td>";	
	$text[] = "	<td><input type='text' name='bijz[$dienst]' value=\"". $data['bijzonderheden'] ."\" size='30'></td>";	
	$text[] = "</tr>";
}

$text[] = "<tr>";
$text[] = "<td colspan='6' align='middle'><input type='submit' name='save' value='Diensten opslaan'>&nbsp;<input type='submit' name='maanden' value='Volgende 3 maanden'></td>";
$text[] = "</tr>";
$text[] = "</table>";
$text[] = "</form>";

if(in_array(1, getMyGroups($_SESSION['ID']))) {
	$text[] = "<a href='?new'>Extra dienst toevoegen</a>";
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>