<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_update = "ALTER TABLE `$TableDiensten` ADD `$DienstVoorganger` TEXT NOT NULL AFTER `$DienstEind`, ADD `$DienstCollecte_1` TEXT NOT NULL AFTER `$DienstVoorganger`, ADD `$DienstCollecte_2` TEXT NOT NULL AFTER `$DienstCollecte_1`, ADD `$DienstOpmerking` TEXT NOT NULL AFTER `$DienstCollecte_2`";
mysqli_query($db, $sql_update);

?>