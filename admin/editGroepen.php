<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(isset($_POST['save'])) {
	if(isset($_REQUEST['new'])) {
		$sql = "INSERT INTO $TableGroups ($GroupNaam, $GroupBeheer) VALUES ('". $_POST['naam'] ."', ". $_POST['beheerder'] .")";
		toLog('info', $_SESSION['ID'], '', 'Groep '. $_POST['naam'] .' toegevoegd');
	} else {
		$sql = "UPDATE $TableGroups SET $GroupNaam = '". $_POST['naam'] ."', $GroupBeheer = ". $_POST['beheerder'] ." WHERE $GroupID = ". $_POST['id'];
		toLog('info', $_SESSION['ID'], '', 'Groep '. $_POST['naam'] .' gewijzigd');
	}
	
	mysqli_query($db, $sql);
	
	$text[] = "Groep opgeslagen";	
} elseif(isset($_REQUEST['id']) OR isset($_REQUEST['new'])) {	
	$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>";
	
	if(isset($_REQUEST['new'])) {
		$text[] = "<input type='hidden' name='new' value=''>";
		$groepData = array('naam' => '', 'beheer' => 0);
	} else {
		$id		= getParam('id', '');
		$groepData = getGroupDetails($id);
		$text[] = "<input type='hidden' name='id' value='$id'>";
	}	
	
	$text[] = "<table>";
	$text[] = "<tr>";
	$text[] = "	<td>Naam</td>";
	$text[] = "	<td><input type='text' name='naam' value='". $groepData['naam'] ."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>Beheerder</td>";
	$text[] = "	<td><select name='beheerder'>";
	$groepen = getAllGroups();
	
	foreach($groepen as $groep) {
		$data = getGroupDetails($groep);
		$text[] = "	<option value='$groep'". ($groep == $groepData['beheer'] ? ' selected' : '').($groep == $id ? ' disabled' : '') .">". $data['naam'] ."</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td rowspan='2'><input type='submit' name='save' value='Opslaan'></td>";
	$text[] = "</tr>";
	$text[] = "</table>";
	$text[] = "</form>";
} else {
	$groepen = getAllGroups();
	
	$text[] = "<a href='?new'>Nieuwe groep toevoegen</a>";
	$text[] = "<p>";
	
	foreach($groepen as $groep) {
		$data = getGroupDetails($groep);
		$text[] = "<a href='?id=$groep'>". $data['naam'] ."</a><br>";
	}	
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>