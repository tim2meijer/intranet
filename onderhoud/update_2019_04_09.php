<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableDiensten ADD $DienstLiturgie TEXT NOT NULL AFTER $DienstOpmerking";
mysqli_query($db, $sql);

?>