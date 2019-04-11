<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableMC ADD $MClastSeen INT(11) NOT NULL AFTER $MCmark, ADD $MClastChecked INT(11) NOT NULL AFTER $MClastSeen";
mysqli_query($db, $sql);

?>