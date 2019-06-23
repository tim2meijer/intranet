<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableGroups ADD $GroupMCTag INT(6) NOT NULL AFTER $GroupBeheer;";
mysqli_query($db, $sql);

$sql = "CREATE TABLE `$TableCommMC` (`$CommMCID` int(7) NOT NULL, `$CommMCGroupID` int(3) NOT NULL, `$ComMClastSeen` int(11) NOT NULL, `$ComMClastChecked` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1";
mysqli_query($db, $sql);

?>