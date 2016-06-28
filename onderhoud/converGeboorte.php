<?php
include_once('../include/functions.php');
include_once('../include/config.php');
#include_once('include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$sql = "SELECT * FROM $TableUsers WHERE $UserGeboorte like '0000-00-00' ORDER BY $UserAchternaam";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		$geboorte = $row[$UserGebJaar].'-'.$row[$UserGebMaand].'-'.$row[$UserGebDag];
		
		$sql_update = "UPDATE $TableUsers SET $UserGeboorte = '$geboorte' WHERE $UserID = ". $row[$UserID];
		mysqli_query($db, $sql_update);		
	} while($row = mysqli_fetch_array($result));
}

		
		
		
		

?>