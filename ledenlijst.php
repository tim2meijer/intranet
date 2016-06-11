<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$letterArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

if(isset($_REQUEST['letter'])) {
	$letter = $_REQUEST['letter'];
} else {
	$letter = 'A';
}

echo $HTMLHeader;
echo "<h1>Ledenlijst</h1>".NL;

foreach($letterArray as $key => $value) {
	if($key > 0) {
		echo ' | ';
	}
	
	if($value == $letter) {
		echo $value;
	} else {
		echo "<a href='?letter=$value'>$value</a>";
	}
}

echo '<p>';

$sql = "SELECT * FROM $TableUsers WHERE $UserAchternaam like '$letter%' ORDER BY $UserAchternaam";
$result = mysqli_query($db, $sql);
if($row	= mysqli_fetch_array($result)) {
	do {
		echo "<a href='profiel.php?id=". $row[$UserID] ."'>". makeName($row[$UserID], 5)."</a><br>";
	} while($row	= mysqli_fetch_array($result));
}

echo $HTMLFooter;
?>