<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$RoosterData = getRoosterDetails($_REQUEST['rooster']);
$diensten = getAllKerkdiensten(true);
//$diensten = getKerkdiensten(time(), mktime(date("H"), date("i"), date("s"), date("n")+3, date("j"), date("Y"));

echo $HTMLHeader;
echo "<h1>". $RoosterData['naam'] ."</h1>".NL;
echo '<table>';

foreach($diensten as $dienst) {
	$details = getKerkdienstDetails($dienst);
	$vulling = getRoosterVulling($_REQUEST['rooster'], $dienst);
	
	if(count($vulling) > 0) {
		$namen = array();
			
		foreach($vulling as $lid) {
			$namen[] = "<a href='profiel.php?id=$lid'>". makeName($lid, 5) ."</a>";
		}
		
		echo '<tr><td valign=\'top\'>'.date("d-m", $details['start']).'</td><td valign=\'top\'>'. implode('<br>', $namen).'</td></tr>'.NL;
	}
}

echo '</table>';
echo $HTMLFooter;
?>