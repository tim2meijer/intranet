<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$wijken[] = 'test.txt';

$onScreen = false;

if(!$onScreen) {
	$db = connect_db();

	$TableUsers .= '_new';
	$TableAdres .= '_new';

	mysqli_query($db, "TRUNCATE $TableUsers");
	mysqli_query($db, "TRUNCATE $TableAdres");
}

$wegschrijven = false;
$straat = $nummer = $postcode = $plaats = '';
$mail = $telefoon = array();

foreach($wijken as $txt_file) {
	$regels = file($txt_file);
	
	foreach($regels as $r => $tempRegel) {
		$regel = utf8_encode($tempRegel);
		#echo '> '. $regel .'<br>';
		
		if(strpos($regel, '2017 Wijk ')) {
			$wijk = substr (trim($regel), -1);
		}
				
		if(!strpos($regel, 'Adressenlijst 3GK')) {			
			# Nieuw persoon
			if(strpos($regel, ' V ') OR strpos($regel, ' M ') OR ($r+1) == count($regels)) {				
				$persoonData = true;
				
				# Als er een komma op dezelfde regel staat als een geslacht
				# staat er een naam én een adres op dezelfde rij
				# even opknippen
				if(strpos($regel, ',')) {
					$delen = explode(' ', $regel);
					
					$keyV = array_search ('V', $delen);
					$keyM = array_search ('M', $delen);
					
					if($keyV == false) {
						$key = array_search ('M', $delen);
					} else {
						$key = $keyV;						
					}
										
					$adresRegel = implode(' ', array_slice($delen, ($key+2)));
					$regel = implode(' ', array_slice($delen, 0, ($key+2)));
				}
																								
				# Eerst even vorig adres inschrijven
				if($wegschrijven OR ($r+1) == count($regels)) {
					
					# kerkelijk adres inschrijven					
					if($onScreen) {
						echo 'straat : '. $straat .'| nummer : '. $nummer .'| PC : '. $postcode .'| Plaats : '. $plaats .'| Mail : '. $mail[0]  .'| Telefoon : '. $telefoon[0]  .'| Wijk : '.  $wijk .'<br>';
					} else {
						$sql = "INSERT INTO $TableAdres (`straat`, `nummer`, `postcode`, `plaats`, `mail`, `telefoon`, `wijk`) VALUES ('". addslashes(trim($straat)) ."', '". trim($nummer) ."', '". trim($postcode) ."', '". addslashes(trim($plaats)) ."', '". trim($mail[0]) ."', '". trim($telefoon[0]) ."', '". trim($wijk) ."')";
					
						if(!mysqli_query($db, $sql)) {
							echo 'ERROR : '. $straat .'|'. $nummer .'|'. $postcode .'|'. $plaats .'|'. $mail[0]  .'|'. $telefoon[0]  .'|'. $wijk .'<br>';
						}						
						$kerkelijkID = mysqli_insert_id($db);								
					}
					
					# personen op dat adres inschrijven
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
						
						if($onScreen) {
							echo $data['voorletters'] .", ". $data['voornaam'] .", ". $data['tussenvoegsel'] .", ". $data['achternaam'] .", ". $email .", ". $tel ."<br>";
						} else {
							$sql = "INSERT INTO $TableUsers (`kerk_adres`, `geslacht`, `voorletters`, `voornaam`, `tussenvoegsel`, `achternaam`, `meisjesnaam`, `geboortedatum`, `telefoon`, `email`, `belijdenis`) VALUES ('$kerkelijkID', '". trim($sexe) ."', '". trim($data['voorletters']) ."', '". addslashes(trim($data['voornaam'])) ."', '". addslashes(trim($data['tussenvoegsel'])) ."', '". addslashes(trim($data['achternaam'])) ."', '". addslashes(trim($data['meisjesnaam'])) ."', '". trim($geb_jaar) .'-'. trim($geb_maand) .'-'. trim($geb_dag) ."', '". trim($tel) ."', '". trim($email) ."', '". trim($soort) ."')";
							if(!mysqli_query($db, $sql)) {
								echo 'ERROR :'.$data['voorletters'] .", ". $data['voornaam'] .", ". $data['tussenvoegsel'] .", ". $data['achternaam'] .", ". $email .", ". $tel ."<br>$sql<br>";
							}
						}					
						
						$email = $tel = $geb_dag = $geb_maand = $geb_jaar = $sexe = $soort = '';
					}
													
					$mail = $telefoon = $persoon = $belijdenis = $geslacht = $geboortedatum = $data_ouder = array();
					$straat = $nummer = $postcode = $plaats = $sexe = '';
				}				
				
				$wegschrijven = false;
				
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
			} else {
				$persoonData = false;
				$wegschrijven = true;
			}
			
			# Adres gegevens
			if(isset($adresRegel) OR !$persoonData) {
				
				if(isset($adresRegel)) {
					$regel = $adresRegel;
					unset($adresRegel);
				}
										
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
}

if(!$onScreen) {
	mysqli_query($db, "UPDATE $TableAdres SET `plaats` = 'DEVENTER' WHERE `plaats` like ''");
}

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