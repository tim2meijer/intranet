<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableRoosters ADD `$RoostersOpmerking` TINYINT(1) NOT NULL AFTER `$RoostersGelijk`";
mysqli_query($db, $sql);

echo $sql;

?>