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
		
		# Alle ouderlingen doorlopen
		if($Oud[0] != '') {
			$ouderlingID = array();			
						
			foreach($Oud as $ouderling) {
				$id = array_search ($ouderling, $namenOud);
				echo $ouderling .' ('. $id .')|';
				$ouderlingID[] = $id;
			}
			echo '<br>';
			
			# Oude data verwijderen
			removeFromRooster(7, $dienstID);
			removeFromRooster(8, $dienstID);
			
			# Nieuwe data inlezen
			foreach($ouderlingID as $id => $ouderling) {			
				if($id == 0) {
					add2Rooster(7, $dienstID, $ouderling, $id);
				} else {
					add2Rooster(8, $dienstID, $ouderling, $id);
				}
			}			
		}
		
		# Alle diakenen doorlopen		
		if($Diak[0] != '') {
			$diakenID = array();
			
			foreach($Diak as $diaken) {
				$id = array_search ($diaken, $namenDiak);
				if($diaken != '') {
					echo $diaken .' ('. $id .')|';
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
				} else {
					add2Rooster(9, $dienstID, $diaken, $id);
				}
			}			
		}		
	}
}
?>
