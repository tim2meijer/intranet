<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableDiensten CHANGE $MCmark $MCstatus SET('subscribe', 'unsubscribe', 'block') NOT NULL DEFAULT 'subscribe';";
mysqli_query($db, $sql);

?>