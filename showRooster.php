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


$text[] = "<h1>". $RoosterData['naam'] ."</h1>".NL;
$block_1[] = '<table>';

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
		
		$block_1[] = "<tr>";
		$block_1[] = "	<td valign='top'>".strftime("%a %d %b %H:%M", $details['start'])."</td>";
		$block_1[] = "	<td valign='top'>". implode('<br>', $namen)."</td>";
		$block_1[] = "</tr>".NL;
	}
}

$block_1[] = '</table>';

$block_2[] = '<table>';
$block_2[] = "<tr>";
$block_2[] = "	<td><a href='showCombineRooster.php?rs=". $_REQUEST['rooster'] ."&pdf'>PDF-versie</a></td>";
$block_2[] = "</tr>".NL;
$block_2[] = '</table>';

echo $HTMLHeader;
echo implode(NL, $text);
echo "<table width=100% border=0>";
echo "<tr>";
echo "	<td width='50%' valign='top'>". showBlock(implode(NL, $block_1), 100)."</td>";
echo "	<td width='50%' valign='top'>". showBlock(implode(NL, $block_2), 100)."</td>";
echo "</tr>";
echo "</table>";
echo $HTMLFooter;
?>
