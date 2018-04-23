<?php
include_once('../include/functions.php');
include_once('../include/config.php');
#include_once('include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$sql = "SELECT * FROM $TableUsers WHERE $UserUsername like '' OR $UserHash like '' ORDER BY $UserVoornaam";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		$id = $row[$UserID];
		$data = getMemberDetails($id);
		
		if($data['username'] == '') {
			$username = generateUsername($id);
			$password = generatePassword(8);
		
			$sql_update = "UPDATE $TableUsers SET $UserUsername = '$username', $UserPassword = '". md5($password) ."' WHERE $UserID = $id";
			mysqli_query($db, $sql_update);
			echo 'Username aangemaakt voor '.  makeName($id, 5) ."($username)<br>\n";
			toLog('info', $_SESSION['ID'], $id, 'account aangemaakt');
		}
		
		if($data['hash'] == '') {
			$hash = generateID($lengthHash);
			
			$sql_update = "UPDATE $TableUsers SET $UserHash = '$hash' WHERE $UserID = $id";
			mysqli_query($db, $sql_update);
			echo 'Hash aangemaakt voor '.  makeName($id, 5) ."<br>\n";
			toLog('info', $_SESSION['ID'], $id, 'hash aangemaakt');
		}	
		
	} while($row = mysqli_fetch_array($result));
}

?>