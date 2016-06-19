<?php
include_once('../include/functions.php');
include_once('../include/config.php');
#include_once('include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$sql = "SELECT * FROM $TableUsers WHERE $UserUsername like '' ORDER BY $UserVoornaam";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		$id = $row[$UserID];
		$data = getMemberDetails($id);
		
		$username = generateUsername($id);
		$password = generatePassword(8);
		
		$sql_update = "UPDATE $TableUsers SET $UserUsername = '$username', $UserPassword = '". md5($password) ."' WHERE $UserID = ". $row[$UserID];
		mysqli_query($db, $sql_update);
		echo $username.'<br>';
		echo '<br>';
		toLog('info', $_SESSION['ID'], $id, 'account aangemaakt');
	} while($row = mysqli_fetch_array($result));
}

		
		
		
		

?>