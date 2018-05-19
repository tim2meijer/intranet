<?php
include_once('../include/config.php');
include_once('../include/functions.php');

# Rooster inlezen
$roosterURL = 'https://docs.google.com/spreadsheets/d/1ZTJ9lzhxNk5PDQCBcLVAwjF-76fVytydW5v6pFTf-yk/pub?output=csv';
$contents = file_get_contents($roosterURL);
$regels = explode("\n", $contents);
$aantal = count($regels);

# Ouderlingen inlezen
$ouderlingen = getGroupMembers(8);
foreach($ouderlingen as $lid) {
	$namenOud[$lid] = makeName($lid, 5);	
}

# Diakenen inlezen
$diakenen = getGroupMembers(9);
foreach($diakenen as $lid) {
	$namenDiak[$lid] = makeName($lid, 5);		
	//echo $lid .' -> '.$namenDiak[$lid].'<br>';
}

# Kerkdiensten inlezen
$start = time() - (30*24*60*60);
$eind = $start + (6*30*24*60*60);
$diensten = getKerkdiensten($start, $eind);

foreach($diensten as $dienst) {
	$details = getKerkdienstDetails($dienst);
	$datumString[$dienst] = date("j-n-Y", $details['start']);
}

for($r=1 ; $r < $aantal ; $r++) {
	$Oud = $Diak = array();
	$regel	= $regels[$r];
	$velden	= explode(",", $regel);
	$a			= count($velden);
	
	$datum		= trim($velden[0]);
		
	for($o=12;$o>9;$o--) {
		$Oud[]		= trim($velden[($a-$o)]);
	}
		
	for($d=8;$d>0;$d--) {
		$Diak[]	= trim($velden[($a-$d)]);
	}
	
	# Als datum bestaat
	if($datum != '') {		
		$dienstID = array_search ($datum, $datumString);
		echo '<b>'. $datum .' ('. $dienstID .')</b><br>';
		
		$vullingOvD	= getRoosterVulling(7, $dienstID);
		$vullingDvD	= getRoosterVulling(10, $dienstID);
		$vullingO		= getRoosterVulling(8, $dienstID);
		$vullingD		= getRoosterVulling(9, $dienstID);
		$details		= getKerkdienstDetails($dienstID);
		
		# Alle ouderlingen doorlopen
		if($Oud[0] != '') {
			$ouderlingID = array();			
						
			foreach($Oud as $ouderling) {
				$id = array_search_closest($ouderling, $namenOud);
				if($ouderling!= '' AND $id != 0) {
					echo $ouderling .' -> '. $namenOud[$id] .' ('. $id .')<br>';
					$ouderlingID[] = $id;
				}
			}
			echo '<br>';
			
			# Oude data verwijderen
			removeFromRooster(7, $dienstID);
			removeFromRooster(8, $dienstID);
			
			# Nieuwe data inlezen
			foreach($ouderlingID as $id => $ouderling) {			
				if($id == 0) {
					add2Rooster(7, $dienstID, $ouderling, $id);
					if($ouderling != $vullingOvD[0])	toLog('info', '', '', 'Wijziging ouderling van dienst '. date("d-m", $details['start']) .': '. makeName($vullingOvD[0], 5) .' -> '. makeName($ouderling, 5));
				} else {
					add2Rooster(8, $dienstID, $ouderling, $id);
					if($ouderling != $vullingO[$id])	toLog('info', '', '', 'Wijziging ouderling '. date("d-m", $details['start']) .': '. makeName($vullingO[$id], 5) .' -> '. makeName($ouderling, 5));
				}
			}			
		}
		
		# Alle diakenen doorlopen		
		if($Diak[0] != '') {
			$diakenID = array();
			
			foreach($Diak as $diaken) {
				$id = array_search_closest ($diaken, $namenDiak);
				if($diaken != '' AND $id != 0) {
					echo $diaken .' -> '. $namenDiak[$id] .' ('. $id .')<br>';
					$diakenID[] = $id;
				}
			}
			echo '<br>';
			
			# Oude data verwijderen
			removeFromRooster(9, $dienstID);
			removeFromRooster(10, $dienstID);
			
			# Nieuwe data inlezen
			foreach($diakenID as $id => $diaken) {
				if($id == 0) {
					add2Rooster(10, $dienstID, $diaken, $id);
					if($diaken != $vullingDvD[0])	toLog('info', '', '', 'Wijziging diaken van dienst '. date("d-m", $details['start']) .': '. makeName($vullingDvD[0], 5) .' -> '. makeName($diaken, 5));
				} else {
					add2Rooster(9, $dienstID, $diaken, $id);
					if($ouderling != $vullingD[$id])	toLog('info', '', '', 'Wijziging diaken '. date("d-m", $details['start']) .': '. makeName($vullingD[$id], 5) .' -> '. makeName($diaken, 5));
				}
			}			
		}		
	}
}
?>
