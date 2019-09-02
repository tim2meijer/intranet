<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableMC ADD $MCrelatie TEXT NOT NULL AFTER $MCstatus, ADD $MCdoop TEXT NOT NULL AFTER $MCrelatie;";
mysqli_query($db, $sql);

?>