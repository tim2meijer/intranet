<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE `$TableVoorganger` ADD `$VoorgangerStijl` SET('0','1') NOT NULL DEFAULT '0' AFTER `$VoorgangerDenom`";
mysqli_query($db, $sql);

?>