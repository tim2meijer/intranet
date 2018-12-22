<?php
include_once('../include/config.php');
include_once('../include/functions.php');
require('../include/excel/SpreadsheetReader.php');

# Rooster inlezen
$roosterURL = 'https://docs.google.com/spreadsheets/d/1W9-kw0go7QLY2GxFyeue8KR7JEWfbogXgiMRxbhfm6M/export?gid=0&format=csv';
$contents = file_get_contents($roosterURL);
$regels = explode("\n", $contents);
$aantal = count($regels);


# Om een of andere reden werkt het niet als je de Excel-file extern laat staan
# Daarom even een locale kopie maken
$roosterURL = 'https://www.dropbox.com/s/0iczm9cczgjqq7a/Collecterooster%202018.xlsx?dl=1';
$xlsFile = 'local_collecterooster.xlsx';
$fp = fopen($xlsFile, 'w+');
fwrite($fp, file_get_contents($roosterURL));
fclose($fp);
# PHP-Class voor het uitlezen van XLS-files aanroepem
$Reader = new SpreadsheetReader($xlsFile);
$Reader -> ChangeSheet(0);



# Schriftlezers inlezen
$schriftlezer = getGroupMembers(13);
foreach($schriftlezer as $lid) {
	$namen[$lid] = makeName($lid, 5);	
}

for($r=1 ; $r < $aantal ; $r++) {
	$lezers = array();	
	$regel	= $regels[$r];
	$velden = str_getcsv($regel);
	$a			= count($velden);
	
	$datum		= trim($velden[0]);
	$lezer_1	= trim($velden[($a-3)]);
	$lezer_2	= trim($velden[($a-1)]);
	
	if($lezer_1 != '')	$lezers[] = $lezer_1;
	if($lezer_2 != '')	$lezers[] = $lezer_2;
	
	if($datum != '') {		
		$datumDelen = explode('-', $datum);
		$start = mktime(0,0,0,$datumDelen[1],$datumDelen[0],$datumDelen[2]);
		$eind = mktime(23,59,59,$datumDelen[1],$datumDelen[0],$datumDelen[2]);
		
		if($eind > time()) {
			$diensten = getKerkdiensten($start, $eind);
			
			if(count($diensten) == count($lezers)) {
				foreach($diensten as $key => $dienstID) {
					echo '<b>'. $datum .' ('. $dienstID .')</b><br>';
					$lezer		= $lezers[$key];
					$vulling	= getRoosterVulling(12, $dienstID);
					$details	= getKerkdienstDetails($dienstID);
										
					//echo $regels[$r].'<br>';
					//echo implode('| ', $velden).'<br>';
					$id = array_search_closest($lezer, $namen);
					if($lezer != '' AND $id != 0) {
						echo $lezer .' -> '. $namen[$id] .' ('. $id .')<br>';
													
						# Oude data verwijderen en nieuwe inladen
						removeFromRooster(12, $dienstID);
						add2Rooster(12, $dienstID, $id,0);
						
						if($id != $vulling[0]) {
							toLog('info', '', $id, 'Wijziging schriftlezer '. date("d-m", $details['start']) .': '. makeName($vulling[0], 5) .' -> '. makeName($id, 5));
							echo 'Was '. makeName($vulling[0], 5) .'<br>';
						} else {
							toLog('debug', '', $id, 'Schriftlezer '. date("d-m", $details['start']) .': '. makeName($id, 5));
						}							
					}
					echo '<br>';
				}
			} else {
				toLog('info', '', '', 'Mismatch diensten en schriftlezers '. date("d-m", $details['start']));
			}
		}
	}
}