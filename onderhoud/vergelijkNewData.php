<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$TableUsersNew = $TableUsers.'_new';
$TableAdresNew = $TableAdres.'_new';

$veldenPersoon = array($UserGeslacht, $UserVoornaam, $UserTussenvoegsel, $UserAchternaam, $UserMeisjesnaam, $UserGeboorte, $UserTelefoon, $UserMail, $UserBelijdenis);
$veldenAdres = array($AdresStraat, $AdresHuisnummer, $AdresPC, $AdresPlaats, $AdresMail, $AdresTelefoon, $AdresWijk);

# Alle huidige ID's opvragen om vervolgens alle gevonden ID's af te strepen
$sql = "SELECT * FROM $TableUsers";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		$id = $row[$UserID];
		$oldIDs[$id] = 0;
	} while($row = mysqli_fetch_array($result));
}		

# Alle nieuwe gegevens opvragen en naast de huidige gegevens leggen
$sql = "SELECT * FROM $TableUsersNew";
$result = mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	do {
		$voorletters	= $row[$UserVoorletters];
		$voornaam			= $row[$UserVoornaam];
		$achternaam		= $row[$UserAchternaam];
		
		$adres				= $row[$UserAdres];
				
		$sql_old = "SELECT * FROM $TableUsers WHERE $UserVoorletters like '$voorletters' AND $UserVoornaam like '$voornaam' AND $UserAchternaam like '$achternaam'";
		$result_old = mysqli_query($db, $sql_old);
		
		if(mysqli_num_rows($result_old) == 0) {
			echo $voornaam .' '. $achternaam . ' niet gevonden<br>';
		} elseif(mysqli_num_rows($result_old) == 1) {
			$row_old = mysqli_fetch_array($result_old);
						
			foreach($veldenPersoon as $veld) {
				if($row[$veld] != '' AND (strtolower(trim($row_old[$veld])) != strtolower(trim($row[$veld]))) AND (str_replace(' ', '', trim($row_old[$veld])) != str_replace(' ', '', trim($row[$veld])))) {
					echo $veld .' is anders voor '.$voornaam .' '. $achternaam .' (was '. strtolower(trim($row_old[$veld])) .'; nu '. strtolower(trim($row[$veld])) .") <a href='$ScriptURL/gegevens.php?id=". $row_old[$UserID]."' target='blank'>wijzig</a> | <a href='$ScriptURL/profiel.php?id=". $row_old[$UserID]."' target='blank'>profiel</a><br>\n"; 
				}
			}
			
			$sql_adres		= "SELECT * FROM $TableAdresNew WHERE $AdresID = $adres";
			$result_adres	= mysqli_query($db, $sql_adres);
			$row_adres = mysqli_fetch_array($result_adres);
			
			$sql_adres_old		= "SELECT * FROM $TableAdres WHERE $AdresID = ". $row_old[$UserAdres];
			$result_adres_old	= mysqli_query($db, $sql_adres_old);
			$row_adres_old = mysqli_fetch_array($result_adres_old);
			
			foreach($veldenAdres as $veld) {
				if($row_adres[$veld] != '' AND (strtolower(trim($row_adres_old[$veld])) != strtolower(trim($row_adres[$veld]))) AND (str_replace(' ', '', trim($row_adres_old[$veld])) != str_replace(' ', '', trim($row_adres[$veld])))) {
					echo '[ADRES] '. $veld .' in adres is anders voor '.$voornaam .' '. $achternaam .' (was '. $row_adres_old[$veld] .'; nu '. $row_adres[$veld] .") <a href='$ScriptURL/gegevens.php?id=". $row_old[$UserID]."' target='blank'>wijzig</a> | <a href='$ScriptURL/profiel.php?id=". $row_old[$UserID]."' target='blank'>profiel</a><br>\n"; 
				}
			}
			
			$id = $row_old[$UserID];
			$oldIDs[$id] = 1;					
		} else {
			echo $voornaam .' '. $achternaam . ' meerdere keren gevonden<br>';
		}
	} while($row = mysqli_fetch_array($result));
}

# Welke huidige ID's zijn niet gevonden in de nieuwe data
foreach($oldIDs as $oldID => $value) {
	if($value == 0) {
		echo "<a href='$ScriptURL/profiel.php?id=$oldID'>". makeName($oldID, 5) ." niet gevonden</a><br>";
	}
}	

?>