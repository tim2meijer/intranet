<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');

$showLogin = true;

$requiredUserGroups = array(1, 20, 22);

if(isset($_REQUEST['hash'])) {
	$id = isValidHash($_REQUEST['hash']);
	
	if(!is_numeric($id)) {
		toLog('error', '', '', 'ongeldige hash (kerkdiensten)');
		$showLogin = true;
	} else {
		$showLogin = false;
		$_SESSION['ID'] = $id;
		toLog('info', $id, '', 'kerkdiensten mbv hash');
		
		$authorisatieArray = getMyGroups($id);
		$overlap = array_intersect ($requiredUserGroups, $authorisatieArray);
		if(count($overlap) == 0) {
			$showLogin = true;
			toLog('error', $id, '', 'geen rechten voor kerkdiensten mbv hash');
		}
	}
}



if($showLogin) {	
	$cfgProgDir = '../auth/';
	include($cfgProgDir. "secure.php");
	$db = connect_db();
}


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

$text[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
$text[] = "<input type='hidden' name='blokken' value='$blokken'>";
$text[] = "<table>";
$text[] = "<tr>";
$text[] = "	<td>Datum</td>";
$text[] = "	<td>Start</td>";
$text[] = "	<td>Eind</td>";
$text[] = "	<td>Voorganger</td>";
$text[] = "	<td>Collecte 1</td>";
$text[] = "	<td>Collecte 2</td>";
$text[] = "	<td>Bijzonderheid</td>";
$text[] = "</tr>";

foreach($diensten as $dienst) {
	$data = getKerkdienstDetails($dienst);
	
	$sMin		= date("i", $data['start']);
	$sUur		= date("H", $data['start']);
	$sDag		= date("d", $data['start']);
	$sMaand	= date("m", $data['start']);
	$sJaar	= date("Y", $data['start']);
	
	$eMin		= date("i", $data['eind']);
	$eUur		= date("H", $data['eind']);
	$eDag		= date("d", $data['eind']);
	$eMaand	= date("m", $data['eind']);
	$eJaar	= date("Y", $data['eind']);
	
	$text[] = "<tr>";
	if(in_array(1, getMyGroups($_SESSION['ID']))) {
		$text[] = "	<td><select name='sDag[$dienst]'>";
		for($d=1 ; $d<32 ; $d++) {
			$text[] = "	<option value='$d'". ($d == $sDag ? ' selected' : '') .">$d</option>";
		}
		$text[] = "	</select>";
		$text[] = "	<select name='sMaand[$dienst]'>";
		for($m=1 ; $m<13 ; $m++) {
			$text[] = "	<option value='$m'". ($m == $sMaand ? ' selected' : '') .">". $maandArray[$m] ."</option>";
		}
		$text[] = "	</select>";
		$text[] = "	<select name='sJaar[$dienst]'>";
		for($j=date("Y"); $j<=(date("Y")+10) ; $j++) {
			$text[] = "	<option value='$j'". ($j == $sJaar ? ' selected' : '') .">". substr($j, -2). "</option>";
		}
		$text[] = "	</select></td>";
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
	} else {
		$text[] = "	<td>". date('j M Y', $data['start']) ."</td>";
		$text[] = "<input type='hidden' name='sDag[$dienst]' value='$sDag'>";
		//$text[] = "<input type='hidden' name='sMaand[$dienst]' value='$sMaand'>";
		//$text[] = "<input type='hidden' name='sJaar[$dienst]' value='$sJaar'>";
		$text[] = "	<td>". date('H:i', $data['start']) ."</td>";
		//$text[] = "<input type='hidden' name='sUur[$dienst]' value='$sUur'>";
		//$text[] = "<input type='hidden' name='sMinuut[$dienst]' value='$sMin'>";
		$text[] = "	<td>". date('H:i', $data['eind']) ."</td>";
		//$text[] = "<input type='hidden' name='eUur[$dienst]' value='$eUur'>";
		//$text[] = "<input type='hidden' name='eMinuut[$dienst]' value='$eMin'>";
	}
	
	if(in_array(1, getMyGroups($_SESSION['ID'])) OR in_array(20, getMyGroups($_SESSION['ID']))) {
		$text[] = "	<td><input type='text' name='voorganger[$dienst]' value=\"". $data['voorganger'] ."\" size='30'></td>";
	} else {
		//$text[] = "	<td><input type='hidden' name='voorganger[$dienst]' value=\"". $data['voorganger'] ."\">". $data['voorganger'] ."</td>";
		$text[] = "	<td>". $data['voorganger'] ."</td>";
	}
	
	if(in_array(1, getMyGroups($_SESSION['ID'])) OR in_array(22, getMyGroups($_SESSION['ID']))) {
		$text[] = "	<td><input type='text' name='collecte_1[$dienst]' value='". $data['collecte_1'] ."'></td>";
		$text[] = "	<td><input type='text' name='collecte_2[$dienst]' value='". $data['collecte_2'] ."'></td>";
	} else {
		//$text[] = "	<td><input type='hidden' name='collecte_1[$dienst]' value='". $data['collecte_1'] ."'>". $data['collecte_1'] ."</td>";
		$text[] = "	<td>". $data['collecte_1'] ."</td>";
		//$text[] = "	<td><input type='hidden' name='collecte_2[$dienst]' value='". $data['collecte_2'] ."'>". $data['collecte_2'] ."</td>";
		$text[] = "	<td>". $data['collecte_2'] ."</td>";
	}
	
	if(in_array(1, getMyGroups($_SESSION['ID'])) OR in_array(20, getMyGroups($_SESSION['ID']))) {
		$text[] = "	<td><input type='text' name='bijz[$dienst]' value=\"". $data['bijzonderheden'] ."\" size='30'></td>";
	} else {
		//$text[] = "	<td><input type='hidden' name='bijz[$dienst]' value=\"". $data['bijzonderheden'] ."\">". $data['bijzonderheden'] ."</td>";
		$text[] = "	<td>". $data['bijzonderheden'] ."</td>";
	}
	$text[] = "<tr>";
}

$text[] = "<tr>";
$text[] = "<td colspan='6' align='middle'><input type='submit' name='save' value='Diensten opslaan'>&nbsp;<input type='submit' name='maanden' value='Volgende 3 maanden'></td>";
$text[] = "</tr>";
$text[] = "</table>";
$text[] = "</form>";
if(in_array(1, getMyGroups($_SESSION['ID']))) {
	$text[] = "<a href='?new'>Extra dienst toevoegen</a>";
}
$text[] = "<p>";

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>