<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(!isset($_REQUEST['groep'])) {
	echo "geen groep gedefinieerd";
	exit;
}

$myGroups = getMyGroups($_SESSION['ID']);
$groupData = getGroupDetails($_REQUEST['groep']);
echo $HTMLHead;
echo $HTMLBody;

if(in_array($_REQUEST['groep'], $myGroups) AND $groupData['html-int'] != "") {
	echo "<h1>". $groupData['naam'] ."</h1>";
	echo '<p>'.NL;
	echo $groupData['html-int'];
} elseif($groupData['html-ext'] != "") {
	echo "<h1>". $groupData['naam'] ."</h1>";
	echo '<p>'.NL;
	echo $groupData['html-ext'];
} else {
	echo "Deze pagina bestaat niet.";
}
	
echo $HTMLFooter;

?>