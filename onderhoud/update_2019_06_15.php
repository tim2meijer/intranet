<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql = "ALTER TABLE $TableGroups ADD $GroupMCTag INT(6) NOT NULL AFTER $GroupBeheer;";
mysqli_query($db, $sql);

$tagCommissie = array(
	2 => 102069,
	7 => 102049,
	8 => 102021,
	9 => 102017,
	11 => 102061,
	15 => 102053,
	13 => 102045,
	24 => 102057,
	19 => 102029,
	21 => 102041,
	27 => 102025
);

foreach($tagCommissie as $id => $tag) {
	$sql = "UPDATE $TableGroups SET $GroupMCTag = $tag WHERE $GroupID =	$id";
	mysqli_query($db, $sql);
}

$sql = "CREATE TABLE `$TableCommMC` (`$CommMCID` int(7) NOT NULL, `$CommMCGroupID` int(3) NOT NULL, `$ComMClastSeen` int(11) NOT NULL, `$ComMClastChecked` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=latin1";
mysqli_query($db, $sql);

?>