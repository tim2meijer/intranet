<?php
include_once('../include/config.php');
include_once('../include/functions.php');
require('../include/excel/SpreadsheetReader.php');

//$db = connect_db();

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

# Alle rijen doorlopen
foreach ($Reader as $Row) {	
	$datumDelen = explode('-', $Row[1]);
	$start = mktime(0,0,0,$datumDelen[0],$datumDelen[1],'20'.$datumDelen[2]);
	$eind = mktime(23,59,59,$datumDelen[0],$datumDelen[1],'20'.$datumDelen[2]);
	
	//echo $Row[1] .'|'. $Row[2] .'{'. $datumDelen[0].'|'.$datumDelen[1].'|'.$datumDelen[2].'>'. date("d-m-Y", $eind) ."}<br>";
	
	# Alleen diensten in de toekomst meenemen/updaten
	if($eind > time()) {
		$collecte_1 = $Row[3];
		$collecte_2 = $Row[4];
		
		$diensten = getKerkdiensten($start, $eind);
		$details = getKerkdienstDetails($diensten[0]);
		
		if(($details['collecte_1'] != $collecte_1) OR ($details['collecte_2'] != $collecte_2)) {
			$sql = "UPDATE $TableDiensten SET $DienstCollecte_1 = '". urlencode($collecte_1) ."', $DienstCollecte_2 = '". urlencode($collecte_2) ."' WHERE $DienstID = ". implode(" OR $DienstID = ", $diensten);
			if(mysqli_query($db, $sql)) {
				echo date("d-m-Y", $start) ."(". $diensten[0] .") -> ". $collecte_1 .'|'. $collecte_2 ."<br>\n";
				toLog('info', '', '', 'Wijziging collectes '. date("d-m", $start) .': '. $details['collecte_1'] .' -> '. $collecte_1 .'; '. $details['collecte_2'] .' -> '. $collecte_2);
			} else {
				toLog('error', '', '', 'Wijziging collecte '. date("d-m", $start) .' ('. $collecte_1 .'; '. $collecte_2 .') ging niet goed');
			}
		} else {
			toLog('debug', '', '', 'Collecte '. date("d-m-Y", $start) ."(". $diensten[0] .") -> ". $collecte_1 .'|'. $collecte_2);
		}	
	}
}
toLog('info', '', '', 'Collectes opnieuw ingelezen');

?>