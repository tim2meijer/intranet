<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableMC ADD $MCtname TEXT NOT NULL AFTER $MCfname";
mysqli_query($db, $sql);

?>