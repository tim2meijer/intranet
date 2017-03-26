<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$requiredUserGroups = array(1);
$cfgProgDir = '../auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(!isset($_REQUEST['groep']) AND !isset($_REQUEST['wijk'])) {
	echo "geen groep of wijk gedefinieerd";
	exit;
}

if(isset($_REQUEST['groep'])) {
	$leden = getGroupMembers($_REQUEST['groep']);
	$groupData = getGroupDetails($_REQUEST['groep']);
	$categorie = $groupData['naam'];
	$file_name = $groupData['naam'].'.csv';
} else {
	$leden = getWijkMembers($_REQUEST['wijk']);
	$categorie = 'Wijk '. $_REQUEST['wijk'];
	$file_name = 'wijk_'.$_REQUEST['wijk'].'.csv';
}

if(isset($_REQUEST['type']) AND $_REQUEST['type'] == 'google') {
	$kop[] = 'Given Name';
	$kop[] = 'Family Name';
	$kop[] = 'Additional Name';
	$kop[] = 'Initials';
	$kop[] = 'Maiden Name';
	$kop[] = 'Birthday';
	//$kop[] = 'Gender';
	$kop[] = 'Group Membership';
	$kop[] = 'E-mail 1 - Type';
	$kop[] = 'E-mail 1 - Value';
	$kop[] = 'E-mail 2 - Type';
	$kop[] = 'E-mail 2 - Value';
	$kop[] = 'Phone 1 - Type';
	$kop[] = 'Phone 1 - Value';
	$kop[] = 'Phone 2 - Type';
	$kop[] = 'Phone 2 - Value';
	$kop[] = 'Address 1 - Type';
	$kop[] = 'Address 1 - Street';
	$kop[] = 'Address 1 - City';
	$kop[] = 'Address 1 - Postal Code';
	
	$output  = implode(";", $kop)."\n";
	
	foreach($leden as $lid) {
		$data = getMemberDetails($lid);
		
		$veld = array();
		$veld[] = $data['voornaam'];
		$veld[] = $data['achternaam'];
		$veld[] = $data['tussenvoegsel'];
		$veld[] = $data['voorletters'];
		$veld[] = $data['meisjesnaam'];
		$veld[] = $data['geboorte'];
		//$veld[] = $data['geslacht'];
		$veld[] = $categorie;
		
		if($data['prive_mail'] == '') {
			$veld[] = 'Home';
			$veld[] = $data['fam_mail'];
			$veld[] = '';
			$veld[] = '';
		} else {
			$veld[] = 'Prive';
			$veld[] = $data['prive_mail'];
			$veld[] = 'Home';
			$veld[] = $data['fam_mail'];
		}
		
		if($data['prive_tel'] == '') {
			$veld[] = 'Home';
			$veld[] = '+31'.str_replace('-','', substr($data['fam_tel'], 1));
			$veld[] = '';
			$veld[] = '';
		} else {
			if(substr($data['prive_tel'], 0, 2) == '06') {
				$veld[] = 'Mobile';
			} else {
				$veld[] = 'Home';
			}
			$veld[] = '+31'.str_replace('-','', substr($data['prive_tel'], 1));
			$veld[] = 'Home';
			$veld[] = '+31'.str_replace('-','', substr($data['fam_tel'], 1));
		}
		
		$veld[] = 'Home';
		$veld[] = $data['straat']	.' '.	$data['huisnummer'];
		$veld[] = ucfirst($data['plaats']);
		$veld[] = $data['PC'];
		
		$output .= implode(";", $veld)."\n";
	}
} else {
	$kop[] = 'Voornaam';
	$kop[] = 'Achternaam';
	$kop[] = 'Tussenvoegsel';
	$kop[] = 'Voorletters';
	$kop[] = 'Meisjesnaam';
	$kop[] = 'Primaire mail';
	$kop[] = 'Secundaire mail';
	
	$output  = implode(";", $kop)."\n";
	
	foreach($leden as $lid) {
	 	$data = getMemberDetails($lid);
	 	
	 	$veld = array();
		$veld[] = $data['voornaam'];
		$veld[] = $data['achternaam'];
		$veld[] = $data['tussenvoegsel'];
		$veld[] = $data['voorletters'];
		$veld[] = $data['meisjesnaam'];
	 	
	 	if($data['prive_mail'] == '') {
			$veld[] = $data['fam_mail'];
			$veld[] = '';
		} else {
			$veld[] = $data['prive_mail'];
			$veld[] = $data['fam_mail'];
		}
		
		$output .= implode(";", $veld)."\n";	 	
	}
}

if(isset($_REQUEST['onscreen'])) {
	echo $output;
} else {
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length:'.strlen($output));
	echo $output;
	exit;
}

?>