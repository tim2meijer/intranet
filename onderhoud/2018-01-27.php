<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_update = "ALTER TABLE $TableUsers ADD $UserScipioID INT(4) NOT NULL AFTER $UserID";
mysqli_query($db, $sql_update);

$sql_update = "ALTER TABLE $TableUsers ADD $UserActief SET('0','1') NOT NULL AFTER $UserID";
mysqli_query($db, $sql_update);

?>