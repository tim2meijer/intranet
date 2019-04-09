<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "CREATE TABLE `$TableMC` (`$MCID` int(7) NOT NULL, `$MCfname` text NOT NULL, `$MClname` text NOT NULL, `$MCmail` text NOT NULL, `$MCwijk` text NOT NULL, `$MCmark` set('0','1') NOT NULL DEFAULT '0' ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
mysqli_query($db, $sql);

?>