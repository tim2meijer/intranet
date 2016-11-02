<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$lidID = $_REQUEST['id'];

$sql_fam = "SELECT $UserAdres FROM $TableUsers WHERE $UserID = $lidID";
$result = mysqli_query($db, $sql_fam);
$row = mysqli_fetch_array($result);
$familieID = $row[$UserAdres];

if($_REQUEST['action'] == 'splits') {
	$sql_adres		= "INSERT INTO $TableAdres ($AdresStraat) VALUES ('nieuwe straat')";
	$result_adres	= mysqli_query($db, $sql_adres);
	$familieID		= mysqli_insert_id($db);
		
	$sql_persoon	= "UPDATE $TableUsers SET $UserAdres = $familieID WHERE $UserID like $lidID";
	mysqli_query($db, $sql_persoon);
	
	$redirectID		= $lidID;
	toLog('info', $_SESSION['ID'], $redirectID, 'persoon verhuisd');
} elseif($_REQUEST['action'] == 'combine') {
} elseif($_REQUEST['action'] == 'add') {
	$sql_adres		= "INSERT INTO $TableAdres ($AdresStraat) VALUES ('nieuwe straat')";
	$result_adres	= mysqli_query($db, $sql_adres);
	$familieID		= mysqli_insert_id($db);
	
	$sql_persoon	= "INSERT INTO $TableUsers ($UserAdres, $UserVoornaam) VALUES ($familieID, 'nieuw persoon')";
	$result				= mysqli_query($db, $sql_persoon);
	$redirectID		= mysqli_insert_id($db);	
	toLog('info', $_SESSION['ID'], $redirectID, 'persoon toegevoegd');			
} elseif($_REQUEST['action'] == 'addFam') {
	$sql_persoon	= "INSERT INTO $TableUsers ($UserAdres, $UserVoornaam) VALUES ($familieID, 'nieuw persoon')";
	$result				= mysqli_query($db, $sql_persoon);
	$redirectID 	= mysqli_insert_id($db);
	toLog('info', $_SESSION['ID'], $redirectID, 'familie toegevoegd');
} elseif($_REQUEST['action'] == 'remove') {
	$familie			= getFamilieleden($lidID);
	$redirectID		= $familie[0];
	
	$sql_persoon	= "DELETE FROM $TableUsers WHERE $UserID = $lidID";
	$result				= mysqli_query($db, $sql_persoon);			
	toLog('info', $_SESSION['ID'], $lidID, 'persoon verwijderd');
} elseif($_REQUEST['action'] == 'removeFam') {
	$sql_persoon	= "DELETE FROM $TableUsers WHERE $UserAdres = $familieID";
	$sql_adres		= "DELETE FROM $TableAdres WHERE $AdresID = $familieID";
	
	$result				= mysqli_query($db, $sql_persoon);
	$result				= mysqli_query($db, $sql_adres);
	toLog('info', $_SESSION['ID'], $lidID, 'familie verwijderd'); 
}

$redirect = $ScriptURL."/gegevens.php?id=".$redirectID;
$url="Location: ". $redirect;
header($url);
	
?>