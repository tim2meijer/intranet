<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();
$filename		= 'SelectieDraijer.csv';
$scipio_raw	= file($filename);

$kop = explode(";", array_shift($scipio_raw));

$GeboorteID = array_search('Geboortedatum', $kop);
$AchternaamID = array_search('Achternaam', $kop);
$RegnrID = array_search('Regnr.', $kop);

foreach($scipio_raw as $rij) {
	$velden = explode(";", $rij);
	$id			= $velden[$RegnrID];
	
	$sql = "SELECT * FROM $TableUsers WHERE $UserScipioID = $id";
	$result = mysqli_query($db, $sql);
	
	if(mysqli_num_rows($result) == 0) {
		$datum	= replaceDatum($velden[$GeboorteID]);
		$naam		= $velden[$AchternaamID];
		
		$sql = "SELECT * FROM $TableUsers WHERE $UserGeboorte like '$datum' AND $UserScipioID = 0";
		//$sql = "SELECT * FROM $TableUsers WHERE $UserAchternaam like '$naam' AND $UserGeboorte like '$datum' AND $UserScipioID = 0";
		//$sql = "SELECT * FROM $TableUsers WHERE $UserMeisjesnaam like '$naam' AND $UserGeboorte like '$datum' AND $UserScipioID = 0";
		//$sql = "SELECT * FROM $TableUsers WHERE $UserAchternaam like '$naam' AND $UserScipioID = 0";
		//$sql = "SELECT * FROM $TableUsers WHERE $UserMeisjesnaam like '$naam' AND $UserScipioID = 0";
		$result = mysqli_query($db, $sql);
		
		if(mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_array($result);
			$sql_update = "UPDATE $TableUsers SET $UserScipioID = $id WHERE $UserID = $row[$UserID]";
			$result = mysqli_query($db, $sql_update);
		} elseif(mysqli_num_rows($result) > 1) {
			echo $naam ." meerdere malen gevonden : ". $velden[$GeboorteID].'|'. $id ."|$sql<br>";
		}
	}
}

function replaceDatum($datum) {
	 $datum = str_replace (' jan ', '-01-' , $datum);
	 $datum = str_replace (' feb ', '-02-' , $datum);
	 $datum = str_replace (' mrt ', '-03-' , $datum);
	 $datum = str_replace (' apr ', '-04-' , $datum);
	 $datum = str_replace (' mei ', '-05-' , $datum);
	 $datum = str_replace (' jun ', '-06-' , $datum);
	 $datum = str_replace (' jul ', '-07-' , $datum);
	 $datum = str_replace (' aug ', '-08-' , $datum);
	 $datum = str_replace (' sep ', '-09-' , $datum);
	 $datum = str_replace (' okt ', '-10-' , $datum);
	 $datum = str_replace (' nov ', '-11-' , $datum);
	 $datum = str_replace (' dec ', '-12-' , $datum);
	 
	 $delen = explode('-', $datum);
	 
	 return $delen[2].'-'.$delen[1].'-'.substr('0'.$delen[0], -2);
}

?>