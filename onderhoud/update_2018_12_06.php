<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableDiensten CHANGE $DienstVoorganger $DienstVoorganger INT NOT NULL;";
mysqli_query($db, $sql);

?>