<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(isset($_POST['save'])) {
	$startTijd = mktime($_POST['sUur'], $_POST['sMin'], 0, $_POST['sMaand'], $_POST['sDag'], $_POST['sJaar']);
	$eindTijd = mktime($_POST['eUur'], $_POST['eMin'], 0, $_POST['eMaand'], $_POST['eDag'], $_POST['eJaar']);
	
	$sql = "UPDATE $TableDiensten SET $DienstStart = '$startTijd', $DienstEind = '$eindTijd' WHERE $DienstID = ". $_POST['id'];
	mysqli_query($db, $sql);
	
	$text[] = "Dienst opgeslagen";	
} elseif(isset($_REQUEST['id'])) {	
	$data = getKerkdienstDetails($_REQUEST['id']);
	
	$sMin		= getParam('sMin', date("i", $data['start']));
	$sUur		= getParam('sUur', date("H", $data['start']));
	$sDag		= getParam('sDag', date("d", $data['start']));
	$sMaand	= getParam('sMaand', date("m", $data['start']));
	$sJaar	= getParam('sJaar', date("Y", $data['start']));
	
	$eMin		= getParam('eMin', date("i", $data['eind']));
	$eUur		= getParam('eUur', date("H", $data['eind']));
	$eDag		= getParam('eDag', date("d", $data['eind']));
	$eMaand	= getParam('eMaand', date("m", $data['eind']));
	$eJaar	= getParam('eJaar', date("Y", $data['eind']));
	
	$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>";
	$text[] = "<input type='hidden' name='id' value='". $_REQUEST['id'] ."'>";
	$text[] = "<table>";
	$text[] = "<tr>";
	$text[] = "	<td>Starttijd</td>";
	$text[] = "	<td><select name='sDag'>";
	for($d=1 ; $d<32 ; $d++) {
		$text[] = "	<option value='$d'". ($d == $sDag ? ' selected' : '') .">$d</option>";
	}
	$text[] = "	</select> - ";
	$text[] = "	<select name='sMaand'>";
	for($m=1 ; $m<13 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $sMaand ? ' selected' : '') .">". $maandArray[$m] ."</option>";
	}
	$text[] = "	</select> - ";
	$text[] = "	<select name='sJaar'>";
	for($j=date("Y"); $j<=(date("Y")+10) ; $j++) {
		$text[] = "	<option value='$j'". ($j == $sJaar ? ' selected' : '') .">$j</option>";
	}
	$text[] = "	</select> ";
	$text[] = "	<select name='sUur'>";
	for($u=0; $u<24 ; $u++) {
		$text[] = "	<option value='$u'". ($u == $sUur ? ' selected' : '') .">$u</option>";
	}
	$text[] = "	</select>:";
	$text[] = "	<select name='sMin'>";
	for($m=0; $m<60 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $sMin ? ' selected' : '') .">". substr('0'.$m, -2) ."</option>";
	}
	$text[] = "	</select></td>";
	
	$text[] = "	<td rowspan='2'>&nbsp;</td>";
	$text[] = "	<td rowspan='2'><input type='submit' name='save' value='Opslaan'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>Eindtijd</td>";
	$text[] = "	<td><select name='eDag'>";
	for($d=1 ; $d<32 ; $d++) {
		$text[] = "	<option value='$d'". ($d == $eDag ? ' selected' : '') .">$d</option>";
	}
	$text[] = "	</select> - ";
	$text[] = "	<select name='eMaand'>";
	for($m=1 ; $m<13 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $eMaand ? ' selected' : '') .">". $maandArray[$m] ."</option>";
	}
	$text[] = "	</select> - ";
	$text[] = "	<select name='eJaar'>";
	for($j=date("Y"); $j<=(date("Y")+10) ; $j++) {
		$text[] = "	<option value='$j'". ($j == $eJaar ? ' selected' : '') .">$j</option>";
	}
	$text[] = "	</select> ";
	$text[] = "	<select name='eUur'>";
	for($u=0; $u<24 ; $u++) {
		$text[] = "	<option value='$u'". ($u == $eUur ? ' selected' : '') .">$u</option>";
	}
	$text[] = "	</select>:";
	$text[] = "	<select name='eMin'>";
	for($m=0; $m<60 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $eMin ? ' selected' : '') .">". substr('0'.$m, -2) ."</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "</tr>";	
	$text[] = "</table>";
	$text[] = "</form>";
} else {
	$diensten = getAllKerkdiensten(true);
	
	foreach($diensten as $dienst) {
		$data = getKerkdienstDetails($dienst);
		$text[] = "<a href='?id=$dienst'>".date("d-m H:i", $data['start']) ."-".date("H:i", $data['eind']) ."</a><br>";
	}
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>