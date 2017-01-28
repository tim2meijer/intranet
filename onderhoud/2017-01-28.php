<?
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_update = "ALTER TABLE `$TableUsers` ADD `$UserHash` TEXT NOT NULL AFTER `$UserPassword`";
mysqli_query($db, $sql_update);

?>