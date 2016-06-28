<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$RoosterData = getRoosterDetails($_REQUEST['rooster']);
$diensten = getAllKerkdiensten(true);
$IDs = getGroupMembers($RoosterData['groep']);

toLog('debug', $_SESSION['ID'], '', 'Rooster '. $RoosterData['naam'] .' bekeken');

echo $HTMLHeader;
echo "<h1>". $RoosterData['naam'] ."</h1>".NL;
echo '<table>';

foreach($diensten as $dienst) {
	$details = getKerkdienstDetails($dienst);
	$vulling = getRoosterVulling($_REQUEST['rooster'], $dienst);
	
	if(count($vulling) > 0) {
		$namen = array();
			
		foreach($vulling as $lid) {
			//$data = getMemberDetails($lid);
			$string = "<a href='profiel.php?id=$lid'>". makeName($lid, 5) ."</a>";
			
			if(in_array($_SESSION['ID'], $IDs)) {
				if($lid == $_SESSION['ID']) {
					$string .= " <a href='ruilen.php?rooster=". $_REQUEST['rooster'] ."&dienst_d=$dienst&dader=$lid' title='klik om ruiling door te geven'><img src='images/wisselen.png'></a>";
				} else {
					$string .= " <a href='ruilen.php?rooster=". $_REQUEST['rooster'] ."&dienst_s=$dienst&slachtoffer=$lid' title='klik ruiling door te geven'><img src='images/wisselen.png'></a>";
				}
			}
			
			$namen[] = $string;
		}
		
		echo '<tr><td valign=\'top\'>'.date("d-m", $details['start']).'</td><td valign=\'top\'>'. implode('<br>', $namen).'</td></tr>'.NL;
	}
}

echo '</table>';
echo $HTMLFooter;
?>