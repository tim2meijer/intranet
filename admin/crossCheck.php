<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$sql = "SELECT * FROM $TablePlanning GROUP BY $PlanningUser";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		$data = getMemberDetails($row[$PlanningUser]);
		
		if($data['mail'] == '') {
			echo makeName($row[$PlanningUser], 5) .' is niet in het bezit van een mailadres<br>';
		}		
	} while($row = mysqli_fetch_array($result));		
}

?>