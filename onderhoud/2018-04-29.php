<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_1 = "ALTER TABLE $TableUsers ADD `$UserHashShort` TEXT NOT NULL AFTER `$UserPassword`";
mysqli_query($db, $sql_1);

$sql_2 = "ALTER TABLE $TableUsers CHANGE `$UserHash` `$UserHashLong` TEXT NOT NULL;";
mysqli_query($db, $sql_2);

$sql_3 = "SELECT * FROM `old_$TableUsers`";
$result_3 = mysqli_query($db, $sql_3);
$row_3 = mysqli_fetch_array($result_3);
do {
	$ScipioID = $row_3[$UserID];
	$hash_kort = $row_3[$UserHash];
	
	$sql_4 = "UPDATE $TableUsers SET $UserHashShort = '$hash_kort' WHERE $UserID = $ScipioID";
	mysqli_query($db, $sql_4);	
} while($row_3 = mysqli_fetch_array($result_3));

?>