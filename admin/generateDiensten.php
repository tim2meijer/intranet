<?php
include_once('include/functions.php');
include_once('include/config.php');
#include_once('include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

for($i=0 ; $i<25 ; $i++) {
	$offset = (7-date("N")) + (7*$i);
	
	$start_1	= mktime(10,0,0,date("n"),(date("j")+$offset), date("Y"));
	$eind_1		= mktime(11,30,0,date("n"),(date("j")+$offset), date("Y"));
	
	$start_2	= mktime(16,30,0,date("n"),(date("j")+$offset), date("Y"));
	$eind_2		= mktime(18,00,0,date("n"),(date("j")+$offset), date("Y"));
	
	echo "INSERT INTO `kerkdiensten` (`start`, `eind`) VALUES ('$start_1', '$eind_1');<br>";
	echo "INSERT INTO `kerkdiensten` (`start`, `eind`) VALUES ('$start_2', '$eind_2');<br>";	
}

?>