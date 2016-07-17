<?
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_update = "ALTER TABLE `$TableUsers` ADD `$UserLastChange` DATETIME NOT NULL AFTER `$UserBelijdenis`, ADD `$UserLastVisit` DATETIME NOT NULL AFTER `$UserLastChange`";
mysqli_query($db, $sql_update);

$sql_update = "ALTER TABLE `$TableRoosters` ADD `$RoostersLastChange` DATETIME NOT NULL AFTER `$RoostersFromAddr`";
mysqli_query($db, $sql_update);

?>