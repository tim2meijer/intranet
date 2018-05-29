<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE `$TableRoosters` ADD `$RoostersTextOnly` TINYINT(1) NOT NULL AFTER `$RoostersOpmerking`";
mysqli_query($db, $sql);

$sql = "CREATE TABLE `$TablePlanningTxt` (`$PlanningTxTDienst` int(5) NOT NULL, `$PlanningTxTGroup` int(3) NOT NULL, `$PlanningTxTText` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1";
mysqli_query($db, $sql);

?>