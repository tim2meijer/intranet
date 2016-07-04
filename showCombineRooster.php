<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(isset($_REQUEST['show'])) {
	$diensten = getAllKerkdiensten(false);
	$roosters = $_REQUEST['r'];
	
	$text[] = "<table border=0>";
	$text[] = "<tr>";
	$text[] = "<td>&nbsp;</td>";
	
	foreach($roosters as $rooster) {
		$RoosterData = getRoosterDetails($rooster);
		$text[] = "<td><b>". $RoosterData['naam'] ."</b></td>";
	}
	$text[] = "</tr>";
	
	foreach($diensten as $dienst) {
		$dienstData = getKerkdienstDetails($dienst);		
		$gevuldeCel = false;
		$cel = array();
				
		foreach($roosters as $rooster) {
			$vulling = getRoosterVulling($rooster, $dienst);
		
			if(count($vulling) > 0) {				
				$team = array();	
				foreach($vulling as $lid) {
					$team[] = makeName($lid, 5);
				}
				$cel[] = "<td valign='top'>". implode("<br>", $team) ."</td>";
				$gevuldeCel = true;
			} else {
				$cel[] = "<td>&nbsp;</td>";
			}		
		}
		
		if($gevuldeCel) {
			$text[] = "<tr>";
			$text[] = "<td valign='top'>".strftime("%a %d %b %H:%M", $dienstData['start'])."</td>";
			$text[] = implode("\n", $cel);
			$text[] = "</tr>";
		}
	}
	$text[] = "</table>";
} else {
	$roosters = getRoosters(0);
	$text[] = "<form>";
	$text[] = "<table>";
	foreach($roosters as $rooster) {
		$data = getRoosterDetails($rooster);
		$text[] = "<tr><td><input type='checkbox' name='r[]' value='$rooster'></td><td>". $data['naam']."</td></tr>";
	}
	$text[] = "<tr><td colspan='2'>&nbsp;</td></tr>";
	$text[] = "<tr><td colspan='2' align='center'><input type='submit' name='show' value='Toon gezamenlijk'></td></tr>";
	$text[] = "</table>";
	$text[] = "</form>";	
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>