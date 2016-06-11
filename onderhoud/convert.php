<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$wijken['A'] = 'wijkA.txt';
$wijken['B'] = 'wijkB.txt';
$wijken['C'] = 'wijkC.txt';
$wijken['D'] = 'wijkD.txt';
$wijken['E'] = 'wijkE.txt';
$wijken['F'] = 'wijkF.txt';
$wijken['G'] = 'wijkG.txt';
$wijken['H'] = 'wijkH.txt';
$wijken['I'] = 'wijkI.txt';
$wijken['J'] = 'wijkJ.txt';

mysqli_query($db, "TRUNCATE $TableUsers");
mysqli_query($db, "TRUNCATE $TableAdres");

$naam = true;
$straat = $nummer = $postcode = $plaats = '';
$vorige_wijk = 'A';

foreach($wijken as $wijk_key => $txt_file) {
	$regels = file($txt_file);
	$new_file = true;
	foreach($regels as $r => $regel) {
		if(!strpos($regel, 'Adressenlijst 3GK')) {	
			# Nieuw persoon
			if(strpos($regel, ' V ') OR strpos($regel, ' M ')) {
				# Eerst even vorig adres inschrijven
				if(!$naam) {
					# kerkelijk adres inschrijven
					$sql = "INSERT INTO $TableAdres (`straat`, `nummer`, `postcode`, `plaats`, `mail`, `telefoon`, `wijk`) VALUES ('". addslashes(trim($straat)) ."', '". trim($nummer) ."', '". trim($postcode) ."', '". addslashes(trim($plaats)) ."', '". trim($mail[0]) ."', '". trim($telefoon[0]) ."', '". trim($wijk_key) ."')";
					if(!mysqli_query($db, $sql)) {
						echo 'ERROR : '. $straat .'|'. $nummer .'|'. $postcode .'|'. $plaats .'|'. $mail[0]  .'|'. $telefoon[0] .'<br>';
					}
					$kerkelijkID = mysqli_insert_id($db);
											
					# leden inschrijven op kerkelijk adres 
					//if($new_file) {
					//	$wijk = $vorige_wijk;
					//	$new_file = false;
					//} else {
					//	$wijk = $wijk_key;
					//}
					
					foreach($persoon as $key => $value) {					
						$data = convertName($value);
						
						if($key == 0) {
							$data_ouder = $data;
						}
						
						if($data['achternaam'] == '') {
							$data['achternaam'] = $data_ouder['achternaam'];
							$data['tussenvoegsel'] = $data_ouder['tussenvoegsel'];
						}
						
						//echo '['. $key ."|". $value ."|". $data['voorletters'] ."|". $data['voornaam'] ."|". $data['tussenvoegsel'] ."|". $data['achternaam'] ."|". $data['meisjesnaam'] .']<br>';
						
						$geb_dag = substr($geboortedatum[$key], 0, 2);
						$geb_maand = substr($geboortedatum[$key], 3, 2);
						$geb_jaar = substr($geboortedatum[$key], 6, 4);
						$sexe = $geslacht[$key];
						$soort = $belijdenis[$key];
						
						if(array_key_exists($key, $mail) AND $key > 0) {
							$email = $mail[$key];
						}
						
						if(array_key_exists($key, $telefoon) AND $key > 0) {
							$tel = $telefoon[$key];
						}
												
						$sql = "INSERT INTO `leden` (`kerk_adres`, `geslacht`, `voorletters`, `voornaam`, `tussenvoegsel`, `achternaam`, `meisjesnaam`, `geboortedag`, `geboortemaand`, `geboortejaar`, `telefoon`, `email`, `belijdenis`) VALUES ('$kerkelijkID', '". trim($sexe) ."', '". trim($data['voorletters']) ."', '". addslashes(trim($data['voornaam'])) ."', '". addslashes(trim($data['tussenvoegsel'])) ."', '". addslashes(trim($data['achternaam'])) ."', '". addslashes(trim($data['meisjesnaam'])) ."', '". trim($geb_dag) ."', '". trim($geb_maand) ."', '". trim($geb_jaar) ."', '". trim($tel) ."', '". trim($email) ."', '". trim($soort) ."')";
						if(!mysqli_query($db, $sql)) {
							echo 'ERROR :'.$data['voorletters'] .", ". $data['voornaam'] .", ". $data['tussenvoegsel'] .", ". $data['achternaam'] .", ". $email .", ". $tel .'<br>';
						}
						$email = $tel = $geb_dag = $geb_maand = $geb_jaar = $sexe = $soort = '';
					}
													
					//echo '<hr>';
					//echo "\n\n\n\n";
													
					$mail = $telefoon = $persoon = $belijdenis = $geslacht = $geboortedatum = $data_ouder = array();
					$straat = $nummer = $postcode = $plaats = $sexe = '';
				}				
				
				$naam = true;
				
				$delen = explode(' ', $regel);
				
				$laatste = array_pop($delen);
				
				if(strlen($laatste) > 3) {
					$mail[] = $laatste;
					$belijdenis[] = array_pop($delen);
				} else {
					$belijdenis[] = $laatste;
				}
				
				$geslacht[] = array_pop($delen);
				$geboortedatum[] = array_pop($delen);
				
				$volledige_naam = implode(' ', $delen);
				$persoon[] = $volledige_naam;
			# Adres gegevens
			} else {
				$naam = false;
			
				# mailadressen
				if(strpos($regel, '@')) {
					$mail[] = $regel;
				}
				
				# adresregel
				if(strpos($regel, ',')) {
					$delen = explode(',', $regel);
					
					$adres_delen = explode(' ', $delen[0]);
					$nummer = array_pop($adres_delen);
					$straat = implode(' ', $adres_delen);
					
					$postcode = substr(trim($delen[1]), 0, 6);
					$plaats = substr(trim($delen[1]), 7);
				}
				
				if($regel[0] == '0' OR strpos($regel, '/')) {
					$telefoon = explode('/', $regel);
				}
			}
		}
	}
	$vorige_wijk = $wijk_key;
}

mysqli_query($db, "UPDATE `adressen` SET `plaats` = 'DEVENTER' WHERE `plaats` like ''");

/*
$namen[] = 'J. (Johan) Bluemink';
$namen[] = 'M.A.D. (Marijke) Bluemink - Sytsma';
$namen[] = 'Gerrit Johannes (Gert-Jan)';
$namen[] = 'Rianne Elisabeth (Rianne)';
$namen[] = 'Sytske Petra (Sytske)';
$namen[] = 'Thomas Christian (Thomas)';
$namen[] = 'J.J.M. van Deursen';
$namen[] = 'E. van Deursen - de Groot';
$namen[] = 'D.R. (Daniël) van Putten';
$namen[] = 'H. (Herma) van Putten - ten Hove';
$namen[] = 'E. (Erik) van der Burg';
$namen[] = 'Daniel';

foreach($namen as $naam) {
	$data = convertName($naam);
	
	echo "<b>$naam</b><br>";
	
	foreach($data as $key => $value) {
		echo $key .' -> '. $value .'<br>';
	}
	
	echo '<hr>';
	
}
*/




function getString($start, $end, $string, $offset) {
	if ($start != '') {
		$startPos = strpos ($string, $start, $offset) + strlen($start);
	} else {
		$startPos = 0;
	}
	
	if ($end != '') {
		$eindPos	= strpos ($string, $end, $startPos);
	} else {
		$eindPos = strlen($string);
	}
		
	$text	= substr ($string, $startPos, $eindPos-$startPos);
	$rest	= substr ($string, $eindPos);
		
	return array($text, $rest);
}

?>