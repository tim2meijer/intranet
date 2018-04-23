<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_4 = "ALTER TABLE $TableUsers ADD `$oldUserScipioID` INT NOT NULL AFTER `$oldUserID`";
mysqli_query($db, $sql_4);

?>