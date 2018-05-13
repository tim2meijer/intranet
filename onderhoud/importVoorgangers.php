<?php
include_once('../include/config.php');
include_once('../include/functions.php');
require('../include/excel/SpreadsheetReader.php');

$db = connect_db();

# Om een of andere reden werkt het niet als je de Excel-file extern laat staan
# Daarom even een locale kopie maken
$roosterURL = 'https://www.dropbox.com/s/r9f7x0rv8qjrr2d/Preekrooster.xlsx?dl=1';
$xlsFile = 'local_preekrooster.xlsx';
$fp = fopen($xlsFile, 'w+');
fwrite($fp, file_get_contents($roosterURL));
fclose($fp);

$Reader = new SpreadsheetReader($xlsFile);
$Reader -> ChangeSheet(1);
foreach ($Reader as $Row) {
	$datumDelen = explode('"', $Row[0]);
	$dag = $datumDelen[4];
	$maand = array_search($datumDelen[2], $maandArrayEng);
	$jaar = $datumDelen[6];
	
	$start = mktime(0,0,0,$maand,($dag+1),($jaar+4));
	$eind = mktime(23,59,59,$maand,($dag+1),($jaar+4));
	
	if($eind > time()) {
		$voorganger = array();
		if($Row[2] != "" AND $Row[2] != "x")	$voorganger[] = replaceVoorganger($Row[2]);		
		if($Row[3] != "" AND $Row[3] != "x")	$voorganger[] = replaceVoorganger($Row[3]);
	
		$diensten = getKerkdiensten($start, $eind);
		
		if(count($diensten) == count($voorganger)) {
			foreach($diensten as $dienst) {
				$details = getKerkdienstDetails($dienst);
				
				if($details['voorganger'] != current($voorganger)) {
					$sql = "UPDATE $TableDiensten SET $DienstVoorganger = '". urlencode(current($voorganger)) ."' WHERE $DienstID = $dienst";
					if(mysqli_query($db, $sql)) {
						echo date("d-m-Y", $start) ."($dienst) -> ". current($voorganger) ."<br>\n";
					}
					toLog('info', '', '', 'Wijziging voorganger '. date("d-m", $start) .': '. $details['voorganger'] .' -> '. current($voorganger));
				}
				next($voorganger);
			}
		}
	}
}

function replaceVoorganger($string) {
	switch (trim($string)) {
		case "Wim":
			$voorganger = "ds. W.M. van Wijk";
      break;
		case "Evert":
			$voorganger = "ds. E. Everts";
			break;
		case "Cor":
			$voorganger = "br. C. Weeda";
			break;
		default:
			$voorganger = $string;
		}
		
		return $voorganger;        
}

?>