<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');

$start = time();
$eind = $start + (7*24*60*60);
$diensten = getKerkdiensten($start, $eind);
$roosters = getRoosters();

foreach($diensten as $dienst) {	
	$details = getKerkdienstDetails($dienst);
	
	if($details['bijzonderheden'] != "") { $postfix = $details['bijzonderheden']; } else { $postfix = ''; }
	
	if(date("H", $details['start']) < 12) {
		$dagdeel = "Ochtenddienst";
	} elseif(date("H", $details['start']) < 18) {
		$dagdeel = "Middagdienst";
	} else {
		$dagdeel = "Avonddienst";
	}
	
	$block_1 = array();
	$block_1[] = "<table width='100%' border=0>";
	$block_1[] = "<tr>";
	$block_1[] = "	<td valign='top' colspan='2'><h1>". $dagdeel .' '. strftime("%d %b", $details['start']).($details['bijzonderheden'] != "" ? ' ('.$details['bijzonderheden'].')' : '').'; '.$details['voorganger']."</h1></td>";
	$block_1[] = "</tr>".NL;
	$block_1[] = "<tr>";
	$block_1[] = "	<td valign='top' width='250'>1ste collecte</td>";
	$block_1[] = "	<td valign='top'>". $details['collecte_1'] ."</td>";
	$block_1[] = "</tr>".NL;
	$block_1[] = "<tr>";
	$block_1[] = "	<td valign='top' width='250'>2de collecte</td>";
	$block_1[] = "	<td valign='top'>". $details['collecte_2'] ."</td>";
	$block_1[] = "</tr>".NL;
	
	foreach($roosters as $rooster) {
		$vulling = getRoosterVulling($rooster, $dienst);
	
		if(count($vulling) > 0) {
			$roosterDetails = getRoosterDetails($rooster);
			$namen = array();
			
			foreach($vulling as $lid) {
				$string = "<a href='profiel.php?id=$lid'>". makeName($lid, 5) ."</a>";
				$namen[] = $string;
			}
				
			$block_1[] = "<tr>";
			$block_1[] = "	<td valign='top' width='250'><a href='showRooster.php?rooster=$rooster'>". $roosterDetails['naam'] ."</a></td>";
			$block_1[] = "	<td valign='top'>". implode('<br>', $namen)."</td>";
			$block_1[] = "</tr>".NL;
		}
	}
	$block_1[] = '</table>';
	
	$dienstBlocken[] = implode(NL, $block_1);
}



echo $HTMLHeader;
echo "<table width=100% border=0>";
foreach($dienstBlocken as $block) {
	echo "<tr>";
	echo "	<td valign='top'>". showBlock($block, 100)."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "	<td>&nbsp;</td>";
	echo "</tr>";
}

echo "</table>";
echo $HTMLFooter;
?>
