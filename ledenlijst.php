<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(isset($_REQUEST['search_member']) OR !isset($_REQUEST['letter'])) {
	$leden = getMembers('all');
	foreach($leden as $lid) {
		$namen[$lid] = makeName($lid, 6);
	}
}

if(isset($_REQUEST['search_member'])) {
	$lidID = array_search($_POST['lid'], $namen);
	$redirect = $ScriptURL."profiel.php?id=".$lidID;

	$url="Location: ". $redirect;
	header($url);
}

if(!isset($_REQUEST['letter'])) {		
	echo $HTMLHead;
	echo "	<link rel='stylesheet' type='text/css' href='http://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css'>".NL;
	echo "	<script src=\"http://code.jquery.com/jquery-1.9.1.js\"></script>".NL;
	echo "	<script src=\"http://code.jquery.com/ui/1.10.2/jquery-ui.js\"></script>".NL;
	echo "	<link rel=\"stylesheet\" href=\"/resources/demos/style.css\" />".NL;
	echo "		<script>".NL;
	echo "		$(function() {".NL;
	echo '		var availableTags = ["'. implode('", "', $namen) ."\"];\n";
	echo "		$( \"#namen\" ).autocomplete({".NL;
	echo "		source: availableTags".NL;
	echo "		});".NL;
	echo "	});".NL;
	echo "</script>".NL;
	echo $HTMLBody;
	$letter = '';
} else {
	echo $HTMLHeader;
	$letter = $_REQUEST['letter'];
}

echo "<h1>Ledenlijst</h1>".NL;
echo '<p>';

$letterArray = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	
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

if(!isset($_REQUEST['letter'])) {
	echo "<form method='post' action='$_SERVER[PHP_SELF]' target='_blank'>";                      
	echo "Voer de naam in van de persoon die u zoekt.<br>";
	echo "<input type='text' name='lid' id=\"namen\" size='50'><br>";
	echo "<br>";
	echo "<input type='submit' name='search_member' value='Lid zoeken'>";
	echo "</form>";	
} else {
	$sql = "SELECT * FROM $TableUsers WHERE $UserAchternaam like '$letter%' ORDER BY $UserAchternaam";		
	$result = mysqli_query($db, $sql);
	if($row	= mysqli_fetch_array($result)) {
		do {
			echo "<a href='profiel.php?id=". $row[$UserID] ."'>". makeName($row[$UserID], 5)."</a><br>";
		} while($row	= mysqli_fetch_array($result));
	}
}
	
echo $HTMLFooter;
?>