<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();
$filename		= 'SelectieDraijer.csv';
$scipio_raw	= file($filename);

$kop = explode(";", array_shift($scipio_raw));

$AchternaamID		= array_search('Achternaam', $kop);
$VoorvoegselID	= array_search('Achternaam (voorv.)', $kop);
$PartnerID			= array_search('Achternaam partner', $kop);
$PartnervoorID	= array_search('Achternaam partner (voorv.)', $kop);
$RoepnaamID			= array_search('Roepnaam', $kop);
$RegnrID				= array_search('Regnr.', $kop);
$VoorlettersID	= array_search('Voorletters', $kop);
$GeboorteID 		= array_search('Geboortedatum', $kop);
$GeslachtID			= array_search('Geslacht', $kop);
$MailID					= array_search('e-mail', $kop);
//= array_search('Naamgebruik', $kop);
//= array_search('Achternaam (gebruikt)', $kop);

$sql = "UPDATE $TableUsers SET $UserActief = '0'";
$result = mysqli_query($db, $sql);

foreach($scipio_raw as $rij) {
	$velden = explode(";", $rij);
	$id			= $velden[$RegnrID];
	
	$sql = "SELECT * FROM $TableUsers WHERE $UserScipioID = $id";
	$result = mysqli_query($db, $sql);
	
	if(mysqli_num_rows($result) == 0) {
		$sql = "INSERT INTO $TableUsers ($UserScipioID) VALUES ($id)";
		if(mysqli_query($db, $sql)) {
			$text[] = $id .' toegevoegd';
		}
	}
	
	$row = mysqli_fetch_array($result);
		
	$GeboorteDatum	= replaceDatum($velden[$GeboorteID]);
	$Achternaam		= $velden[$AchternaamID];
	$Voorvoegsel	= $velden[$VoorvoegselID];
	$Partnernaam	= $velden[$PartnerID];
	$Partnervoor	= $velden[$PartnervoorID];
	$Roepnaam			= $velden[$RoepnaamID];
	$Letters			= $velden[$VoorlettersID];
	$Geslacht			= $velden[$GeslachtID];
	$Mail					= $velden[$MailID];
	
	//if($Partnervoor == '') { $Partner = $Partnernaam; } else { $Partner = $Partnervoor .' '.$Partnernaam; }
	if($Voorvoegsel == '') { $Achter = $Achternaam; } else { $Achter = $Voorvoegsel .' '.$Achternaam; }
	
	$sql = "UPDATE $TableUsers SET ";
	$sql .= "$UserGeboorte = '$GeboorteDatum', ";
	
	if($Geslacht == 'Vrouw' AND $Partnernaam != "") {
		$sql .= "$UserAchternaam =  '$Partnernaam', ";
		$sql .= "$UserTussenvoegsel = '$Partnervoor', ";
		$sql .= "$UserMeisjesnaam = '$Achter', ";
	} else {
		$sql .= "$UserAchternaam = '$Achternaam', ";
		$sql .= "$UserTussenvoegsel = '$Voorvoegsel', ";
		$sql .= "$UserMeisjesnaam = '', ";
	}
	
	$sql .= "$UserVoornaam = '$Roepnaam', ";
	$sql .= "$UserVoorletters = '$Letters', ";
	$sql .= "$UserGeslacht = '". $Geslacht[0] ."', ";		
	if($Mail != '')	$sql .= "$UserMail = '$Mail', ";		
	$sql .= "$UserActief = '1' ";
	$sql .= "WHERE $UserScipioID = $id";
		
	if(mysqli_query($db, $sql)) {
		$text[] = makeName($row[$UserID], 13) .' bijgewerkt';
	}
}

echo implode('<br>', $text);

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