<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableRoosters ADD $RoostersReminder SET('0', '1') NOT NULL DEFAULT '1' AFTER $RoostersFields";
mysqli_query($db, $sql);

?>