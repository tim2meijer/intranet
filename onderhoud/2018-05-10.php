<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "CREATE TABLE `$TableRoosOpm` (`$RoosOpmID` int(11) NOT NULL, `$RoosOpmRoos` int(11) NOT NULL, `$RoosOpmDienst` int(11) NOT NULL, `$RoosOpmOpmerking` text NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1";
mysqli_query($db, $sql);

?>