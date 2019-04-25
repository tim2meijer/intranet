<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableMC ADD ". $MCstatus ."_new SET('subscribed', 'unsubscribed', 'block') NOT NULL DEFAULT 'subscribed' AFTER $MCstatus;";
mysqli_query($db, $sql);

$sql = "UPDATE $TableMC SET ". $MCstatus ."_new = 'unsubscribed' WHERE $MCstatus like 'unsubscribe'";
mysqli_query($db, $sql);

$sql = "UPDATE $TableMC SET ". $MCstatus ."_new = 'block' WHERE $MCstatus like 'block'";
mysqli_query($db, $sql);

$sql = "ALTER TABLE $TableMC DROP $MCstatus;";
mysqli_query($db, $sql);

$sql = "ALTER TABLE $TableMC CHANGE ". $MCstatus ."_new $MCstatus SET('subscribed','unsubscribed','block') NOT NULL DEFAULT 'subscribed';";
mysqli_query($db, $sql);

?>