<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$sql = "SELECT $TablePlanning.$PlanningUser FROM $TablePlanning, $TableUsers, $TableAdres WHERE  $TablePlanning.$PlanningUser = $TableUsers.$UserID AND $TableUsers.$UserAdres = $TableAdres.$AdresID AND $TableAdres.$AdresMail like '' AND $TableUsers.$UserMail like '' GROUP BY $TablePlanning.$PlanningUser";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		echo makeName($row[$PlanningUser], 5) .' ['. $row[$PlanningUser] .'] is niet in het bezit van een mailadres<br>';
	} while($row = mysqli_fetch_array($result));		
}

/*
$sql = "SELECT * FROM $TablePlanning, $TableGrpUsr, $TableDiensten WHERE $TablePlanning.$PlanningGroup = $TableGrpUsr.$GrpUsrGroup AND $TablePlanning.$PlanningUser = $TableGrpUsr.$GrpUsrUser AND $TablePlanning.$PlanningDienst = $TableDiensten.$DienstID AND $TableDiensten.$DienstStart > ". time();

echo $sql;


$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		echo makeName($row[$PlanningUser], 5) .' ['. $row[$PlanningUser] .'] is niet in het bezit van een mailadres<br>';
	} while($row = mysqli_fetch_array($result));		
}
*/

$roosters = getRoosters();

foreach($roosters as $rooster) {
	$details = getRoosterDetails($rooster);
	$leden = getGroupMembers($details['groep']);
	
	$sql = "SELECT $TablePlanning.$PlanningUser FROM $TablePlanning, $TableDiensten WHERE $TablePlanning.$PlanningGroup = ". $details['groep'] ." AND $TablePlanning.$PlanningDienst = $TableDiensten.$DienstID  AND $TableDiensten.$DienstStart > ". time();
		
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			makeName($row[$PlanningUser], 5) .' gevonden<br>';
			
			$key = array_search($row[$PlanningUser], $leden);
			unset($leden[$key]);
		} while($row = mysqli_fetch_array($result));		
	}	
	
	foreach($leden as $lid) {
		echo makeName($lid, 5) .' ['. $lid .'] staat niet op het rooster voor '. $details['naam'] .'<br>';
	}	
}


?>