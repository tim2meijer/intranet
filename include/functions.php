<?php
function connect_db() {
	global $dbHostname, $dbUsername, $dbPassword, $dbName;
	
	$link = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbName) or die("Error " . mysqli_error($link));
	mysqli_set_charset($link, 'utf8mb4');
	
	return $link;
}

function generateUsername($id) {
	$data = getMemberDetails($id);
	
	if($data['voorletters'] != '') {
		$voor = strtoupper(str_replace('.', '', $data['voorletters']));
	}
	
	$achter = ucfirst($data['achternaam']);
	
	$username = $voor.$achter;
	
	while(!isUniqueUsername($username)) {
		if($data['meisjesnaam'] != '') {
			$username = $voor.$achter.ucfirst($data['meisjesnaam']);
		} elseif($data['voornaam'] != '') {
			$username = ucfirst($data['voornaam']).$achter;
		} else {
			$username = $voor.$achter.$i;
			$i++;
		}
	}
	
	return $username;	
}

function isUniqueUsername($username) {
	global $TableUsers, $UserUsername;
	$db = connect_db();
	
	$sql = "SELECT * FROM $TableUsers WHERE $UserUsername like '$username'";
	$result = mysqli_query($db, $sql);
	if(mysqli_num_rows($result) == 0) {
		return true;
	} else {
		return false;
	}
}

function generatePassword ($length = 8) {
	// start with a blank password
	$password = "";
	#$possible = "";
	
	// define possible characters - any character in this string can be
	// picked for use in the password, so if you want to put vowels back in
  // or add special characters such as exclamation marks, this is where
  // you should do it
  //$possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#$%&";
  #$possible .= "1234567890";
  #$possible .= "bcdfghjkmnpqrtvwxyz";
  #$possible .= "BCDFGHJKLMNPQRTVWXYZ";
  #$possible .= "!#$%&";
  
  $klink[] = 'a';
  $klink[] = 'e';
  $klink[] = 'i';
  $klink[] = 'o';
  $klink[] = 'u';
  $klink[] = 'ei';
  $klink[] = 'ij';
  $klink[] = 'ie';
  
  $mede[] = 'b';
  $mede[] = 'c';
  $mede[] = 'd';
  $mede[] = 'f';
  $mede[] = 'g';
  $mede[] = 'h';  
  $mede[] = 'j';
  $mede[] = 'k';
  $mede[] = 'l';
  $mede[] = 'm';
  $mede[] = 'n';
  $mede[] = 'p';
  $mede[] = 'q';
  $mede[] = 'r';
  $mede[] = 's';
  $mede[] = 't';
  $mede[] = 'v';
  $mede[] = 'w';
  $mede[] = 'x';
  $mede[] = 'y';
  $mede[] = 'z';
  $mede[] = 'ch';
    
  // we refer to the length of $possible a few times, so let's grab it now
  #$maxlength = count($possible);
  
  // check for length overflow and truncate if necessary
  #if ($length > $maxlength) {
  #	$length = $maxlength;
  #}
  
  $len_klink = count($klink);
  $len_mede = count($mede);
  
  // set up a counter for how many characters are in the password so far
  $i = 0;
  
  // add random characters to $password until $length is reached
  while(strlen($password) < $length) { 
  	if(fmod($i, 2) == 0) {
  		$id = mt_rand(0, $len_mede-1);
  		$char = $mede[$id];
  	} else {
  		$id = mt_rand(0, $len_klink-1);
  		$char = $klink[$id];
  	}
  	  	
  	$password .= $char;
    $i++;
  }
  
  // done!
  return ucfirst($password);
}

function getAllKerkdiensten($fromNow = false) {
	global $TableDiensten, $DienstID, $DienstEind;
	$db = connect_db();
		
	if($fromNow) {
		$startTijd = time();
	} else {
		$startTijd = time() - (31*24*60*60);
	}
	
	$sql = "SELECT $DienstID FROM $TableDiensten WHERE $DienstEind > $startTijd ORDER BY $DienstEind ASC";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$id[] = $row[$DienstID];
		} while($row = mysqli_fetch_array($result));
	}
	return $id;
}

function getKerkdiensten($startTijd, $eindTijd) {
	global $TableDiensten, $DienstID, $DienstEind;
	$db = connect_db();
			
	$sql = "SELECT $DienstID FROM $TableDiensten WHERE $DienstEind BETWEEN $startTijd AND $eindTijd ORDER BY $DienstEind ASC";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$id[] = $row[$DienstID];
		} while($row = mysqli_fetch_array($result));
	}
	return $id;
}


function getKerkdienstDetails($id) {
	global $TableDiensten, $DienstID, $DienstStart, $DienstEind;
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT * FROM $TableDiensten WHERE $DienstID = $id";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		$data['start']	= $row[$DienstStart];
		$data['eind']		= $row[$DienstEind];
	}
	return $data;
}

function getMembers($type = 'all') {
	global $TableUsers, $UserID, $UserAdres, $UserGeboorte, $UserAchternaam, $UserMeisjesnaam;	
	$db = connect_db();
	
	$data = array();
	
	if($type == 'all') {
		$sql = "SELECT $UserID FROM $TableUsers ORDER BY $UserAchternaam";
	} elseif($type == 'volwassen') {
		$sql = "SELECT $UserID FROM $TableUsers WHERE $UserGeboorte < '". (date("Y")-18) ."-". date("m-d") ."' ORDER BY $UserAchternaam";
	} elseif($type == 'adressen') {
		$sql = "SELECT $UserID FROM $TableUsers GROUP BY $UserAdres ORDER BY $UserAchternaam";
	}
		
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$UserID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getGroupMembers($commID) {
	global $TableGrpUsr, $GrpUsrGroup, $GrpUsrUser;
	global $TableUsers, $UserID, $UserAchternaam;
	$db = connect_db();
	
	$data = array();
	$sql = "SELECT $TableGrpUsr.$GrpUsrUser FROM $TableGrpUsr, $TableUsers WHERE $TableUsers.$UserID = $TableGrpUsr.$GrpUsrUser AND $TableGrpUsr.$GrpUsrGroup = $commID ORDER BY $TableUsers.$UserAchternaam";
	
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$GrpUsrUser];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getMemberDetails($id) {
	global $TableUsers, $UserID, $UserAdres, $UserGeslacht, $UserUsername, $UserVoorletters, $UserVoornaam, $UserTussenvoegsel, $UserAchternaam, $UserMeisjesnaam, $UserGeboorte, $UserTelefoon, $UserMail, $UserTwitter, $UserFacebook, $UserLinkedin, $UserBelijdenis;
	global $TableAdres, $AdresID, $AdresStraat, $AdresHuisnummer, $AdresPC, $AdresPlaats, $AdresTelefoon, $AdresMail, $AdresWijk;
	
	$db = connect_db();
	
	$data = array();
		
	$sql = "SELECT * FROM $TableUsers WHERE $UserID = $id";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($result);
		
	$data['id']							= $row[$UserID];
	$data['adres']					= $row[$UserAdres];
	$data['geslacht']				= $row[$UserGeslacht];
	$data['belijdenis']			= $row[$UserBelijdenis];
	$data['voorletters']		= $row[$UserVoorletters];
	$data['voornaam']				= $row[$UserVoornaam];
	$data['tussenvoegsel']	= $row[$UserTussenvoegsel];
	$data['achternaam']			= $row[$UserAchternaam];
	$data['meisjesnaam']		= $row[$UserMeisjesnaam];
	$data['username']				= $row[$UserUsername];
	$data['geboorte']				= $row[$UserGeboorte];		
	$data['jaar']						= substr($row[$UserGeboorte], 0, 4);
	$data['maand']					= substr($row[$UserGeboorte], 5, 2);
	$data['dag']						= substr($row[$UserGeboorte], 8, 2);	
	$data['geb_unix']				= mktime(0,0,0,$data['maand'],$data['dag'],$data['jaar']);
	$data['prive_tel']			= $row[$UserTelefoon];
	$data['prive_mail']			= $row[$UserMail];
	$data['twitter']				= $row[$UserTwitter];
	$data['fb']							= $row[$UserFacebook];
	$data['linkedin']				= $row[$UserLinkedin];
		
	$sql_a = "SELECT * FROM $TableAdres WHERE $AdresID = ". $row[$UserAdres];
	$result_a = mysqli_query($db, $sql_a);
	$row_a = mysqli_fetch_array($result_a);
	
	$data['straat']					= $row_a[$AdresStraat];
	$data['huisnummer']			= $row_a[$AdresHuisnummer];
	$data['PC']							= $row_a[$AdresPC];
	$data['plaats']					= $row_a[$AdresPlaats];		
	$data['fam_tel']				= $row_a[$AdresTelefoon];
	$data['fam_mail']				= $row_a[$AdresMail];
	$data['wijk']						= $row_a[$AdresWijk];
	
	if($data['prive_tel'] == '') {
		$data['tel']					= $data['fam_tel'];
	} else {
		$data['tel']					= $data['prive_tel'];
	}
	
	if($data['prive_mail'] == '') {
		$data['mail']					= $data['fam_mail'];
	} else {
		$data['mail']					= $data['prive_mail'];
	}

	return $data;
}

function getAllGroups() {
	global $TableGroups, $GroupID;	
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT $GroupID FROM $TableGroups";
		
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$GroupID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getMyGroups($id) {
	global $TableGrpUsr, $GrpUsrGroup, $GrpUsrUser;	
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT $GrpUsrGroup FROM $TableGrpUsr WHERE $GrpUsrUser = $id";
		
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$GrpUsrGroup];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getMyGroupsBeheer($id) {
	global $TableGroups, $TableGrpUsr, $GroupBeheer, $GrpUsrGroup, $GrpUsrUser, $GroupID;
	$db = connect_db();
	$data = array();
	
	$sql = "SELECT $TableGroups.$GroupID FROM $TableGroups, $TableGrpUsr WHERE $TableGroups.$GroupBeheer = $TableGrpUsr.$GrpUsrGroup AND $TableGrpUsr.$GrpUsrUser = $id";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$GroupID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getGroupDetails($id) {
	global $TableGroups, $GroupID, $GroupNaam, $GroupHTMLIn, $GroupHTMLEx, $GroupShowIn, $GroupShowEx, $GroupBeheer;
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT * FROM $TableGroups WHERE $GroupID = '$id'";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		$data['naam']	= $row[$GroupNaam];
		$data['html-int']	= urldecode($row[$GroupHTMLIn]);
		$data['html-ext']	= urldecode($row[$GroupHTMLEx]);		
		$data['beheer']	= $row[$GroupBeheer];
	}
	return $data;	
}

function getRoosters($id = 0) {
	global $TableRoosters, $RoostersID, $RoostersNaam, $RoostersGroep, $TableGrpUsr, $GrpUsrGroup, $GrpUsrUser;
	$db = connect_db();
	
	$data = array();
	
	if($id == 0) {
		$sql = "SELECT $RoostersID FROM $TableRoosters ORDER BY $RoostersNaam ASC";
	} else {
		$sql = "SELECT $TableRoosters.$RoostersID FROM $TableRoosters, $TableGrpUsr WHERE $TableGrpUsr.$GrpUsrGroup = $TableRoosters.$RoostersGroep AND $TableGrpUsr.$GrpUsrUser = $id";
	}
	
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$RoostersID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getMyRoostersBeheer($id) {
	global $TableRoosters, $TableGroups, $TableGrpUsr, $RoostersGroep, $RoostersID, $GroupID, $GroupBeheer, $GrpUsrGroup, $GrpUsrUser;
	$db = connect_db();
	
	$data = array();
		
	$sql = "SELECT $TableRoosters.$RoostersID FROM $TableRoosters, $TableGroups, $TableGrpUsr WHERE $TableRoosters.$RoostersGroep = $TableGroups.$GroupID AND $TableGroups.$GroupBeheer = $TableGrpUsr.$GrpUsrGroup AND $TableGrpUsr.$GrpUsrUser = $id";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$RoostersID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getRoosterDetails($id) {
	global $TableRoosters, $RoostersID, $RoostersNaam, $RoostersGroep, $RoostersMail, $RoostersSubject, $RoostersFrom, $RoostersFromAddr;
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT * FROM $TableRoosters WHERE $RoostersID = '$id'";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		$data['naam']	= $row[$RoostersNaam];
		$data['groep']	= $row[$RoostersGroep];
		$data['text_mail']	= urldecode($row[$RoostersMail]);
		$data['onderwerp_mail']	= urldecode($row[$RoostersSubject]);		
		$data['naam_afzender']	= urldecode($row[$RoostersFrom]);
		$data['mail_afzender']	= urldecode($row[$RoostersFromAddr]);
	}
	return $data;	
}

function getBeheerder($groep) {
	global $TableGroups, $GroupID, $GroupBeheer; 
	$db = connect_db();
	
	$sql = "SELECT $GroupBeheer FROM $TableGroups WHERE $GroupID = $groep";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($result);
	
	if($row[$GroupBeheer] != 0) {
		return array($row[$GroupBeheer]);
	} else {
		return array();
	}	
}

function getBeheerder4Rooster($rooster) {
	global $TableRoosters, $RoostersGroep, $RoostersID, $TableGroups, $GroupID, $GroupBeheer;
	$db = connect_db();
	
	/*
	$sql = "SELECT $TableRoosters.$RoostersID FROM $TableRoosters, $TableGroups, $TableGrpUsr WHERE 
	$TableRoosters.$RoostersGroep = $TableGroups.$GroupID AND
	$TableGroups.$GroupBeheer = $TableGrpUsr.$GrpUsrGroup AND
	$TableGrpUsr.$GrpUsrUser = $id";
	*/
	
	$sql = "SELECT $TableGroups.$GroupBeheer FROM $TableRoosters, $TableGroups WHERE $TableRoosters.$RoostersGroep = $TableGroups.$GroupID AND $TableRoosters.$RoostersID = $rooster";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($result);
	
	return $row[$GroupBeheer];	
}

function addGroupLid($lidID, $commID) {
	global $TableGrpUsr, $GrpUsrGroup, $GrpUsrUser;	
	$db = connect_db();
	
	$sql = "INSERT INTO $TableGrpUsr ($GrpUsrGroup, $GrpUsrUser) VALUES ($commID, $lidID)";
	if(mysqli_query($db, $sql)) {
		return true;
	} else {
		return false;
	}
}

function removeGroupLeden($commID) {
	global $TableGrpUsr, $GrpUsrGroup;	
	$db = connect_db();
	
	$sql = "DELETE FROM $TableGrpUsr WHERE $GrpUsrGroup = $commID";
	if(mysqli_query($db, $sql)) {
		return true;
	} else {
		return false;
	}
}

function removeFromRooster($rooster, $dienst) {
	global $TablePlanning, $PlanningDienst, $PlanningGroup;
	$db = connect_db();
	
	$sql = "DELETE FROM $TablePlanning WHERE $PlanningDienst = $dienst AND $PlanningGroup = $rooster";
	if(mysqli_query($db, $sql)) {
		return true;
	} else {
		return false;
	}
}

function add2Rooster($rooster, $dienst, $persoon, $positie) {
	global $TablePlanning, $PlanningDienst, $PlanningGroup, $PlanningUser, $PlanningPositie;
	$db = connect_db();
	
	$sql = "INSERT INTO $TablePlanning ($PlanningDienst, $PlanningGroup, $PlanningPositie, $PlanningUser) VALUES ($dienst, $rooster, $positie, $persoon)";
	if(mysqli_query($db, $sql)) {
		return true;
	} else {
		return false;
	}
}

function getRoosterVulling($rooster, $dienst) {
	global $TablePlanning, $PlanningDienst, $PlanningGroup, $PlanningUser, $PlanningPositie;
	$db = connect_db();
	
	$data = array();
		
	$sql = "SELECT $PlanningUser FROM $TablePlanning WHERE $PlanningDienst = $dienst AND $PlanningGroup = $rooster ORDER BY $PlanningPositie ASC";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			//$pos = $row[$PlanningPositie];
			//$data[$pos] = $row[$PlanningUser];
			$data[] = $row[$PlanningUser];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function convertName($naam) {
	$data['voorletters'] = '';
	$data['voornaam'] = '';
	$data['tussenvoegsel'] = '';
	$data['achternaam'] = '';
	$data['meisjesnaam'] = '';
	
	if(strpos($naam, ' - ')) {
		$delen = explode('-', $naam);
		$data['meisjesnaam'] = trim($delen[1]);
		$string = trim($delen[0]);
	} else {
		$string = $naam;
	}
	
	if(strpos($naam, '(')) {
		$temp = getString('', '(', $string, 0);
		$data['voorletters'] = $temp[0];
		
		$temp = getString('(', ')', $temp[1], 0);
		$data['voornaam'] = $temp[0];
		
		if($temp[1] != ')') {
			$delen = explode(' ', substr($temp[1], 2));
			$data['achternaam'] = array_pop($delen);
			
			if(count($delen) > 0) {				
				$data['tussenvoegsel'] = implode(' ', $delen);
			}
		}
	} else {
		$delen = explode(' ', $string);
		
		if(count($delen) == 1) {
			$data['voornaam'] = $string;
			$data['voorletters'] = $string[0].'.';
		} else {
			$data['achternaam'] = array_pop($delen);
			$data['voorletters'] = array_shift($delen);	
				
			if(count($delen) > 0) {
				$data['tussenvoegsel'] = implode(' ', $delen);
			}
		}
	}
	
	if(!strpos($data['voorletters'], '.')) {
		$delen = explode(' ', $data['voorletters']);
		
		foreach($delen as $naam) {
			$voorletter[] = $naam[0];
		}
		
		$data['voorletters'] = implode('.', $voorletter);
	}

	return $data;
}

function makeName($id, $type) {
	global $TableUsers, $UserID, $UserVoorletters, $UserVoornaam, $UserTussenvoegsel, $UserAchternaam, $UserMeisjesnaam;
	$db = connect_db();
	
	$sql = "SELECT * FROM $TableUsers WHERE $UserID = $id";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_array($result);
	
	$voorletters = $row[$UserVoorletters];
	
	if($row[$UserVoornaam] != '') {
		$voornaam	= ucfirst($row[$UserVoornaam]);		
	} else {
		$voornaam = $voorletters;
	}
	
	$tussen 	= strtolower($row[$UserTussenvoegsel]);
	$achter 	= ucfirst($row[$UserAchternaam]);
	
	if($row[$UserMeisjesnaam] != '') {
		$achter_m = $row[$UserMeisjesnaam];
	} else {
		$achter_m = '';
	}
	
	# 1 = voornaam												Alberdien
	# 2 = korte achternaam								Jong
	# 3 = volledige achternaam (man)			de Jong
	# 4 = volledige achternaam (vrouw)		de Jong-van Ginkel
	# 5 = voornaam achternaam (man)				Alberdien de Jong
	# 6 = voornaam achternaam (vrouw)			Alberdien de Jong-van Ginkel
	# 7 = voornaam achternaam (vrouw)			Alberdien van Ginkel	
	# 8 = achternaam, voornaam						Jong; de, Alberdien
	# 9 = voorletters achternaam (man)		A. de Jong
	# 10 = voorletters achternaam (vrouw)	A. de Jong-van Ginkel
	# 11 = voorletters achternaam (vrouw)	A. van Ginkel
	# 12 = voorletters achternaam (man)		A. (Alberdien) de Jong
	# 13 = voorletters achternaam (vrouw)	A. (Alberdien) de Jong-van Ginkel
	# 14 = voorletters achternaam (vrouw)	A. (Alberdien) van Ginkel

	
	if($achter_m != '' AND ($type == 4 OR $type == 6 OR $type == 10)) {
		$achter .= '-'.$achter_m;
	} elseif($achter_m != '' AND ($type == 7 OR $type == 11)) {
		$achter = $achter_m;
	}
			
	if($tussen == '') {
		$tussenvoegsel	= '';
		$achternaam	= $achter;
	} else {
		$tussenvoegsel= $tussen;
		
		if($type == 2 OR $type == 7) {
			$achternaam	= $achter;
		} elseif($type == 8) {
			$achternaam	= $achter.'; '.$tussen;
		} else {
			$achternaam	= $tussen.' '.$achter;
		}
	}
		
	if($type == 1) {
		return urldecode($voornaam);
	} elseif($type == 2) {
		return urldecode($achternaam);
	} elseif($type == 3 OR $type == 4) {
		return urldecode($achternaam);
	} elseif($type == 5 OR $type == 6 OR $type == 7) {
		return urldecode($voornaam.' '.$achternaam);
	} elseif($type == 8) {
		return urldecode($achternaam.', '.$voornaam);
	} elseif($type == 9 OR $type == 10 OR $type == 11) {
		return urldecode($voorletters .' '. $achternaam);
	} elseif($type == 12 OR $type == 13 OR $type == 14) {
		if($voornaam != $voorletters) {
			return urldecode($voorletters .' ('. $voornaam .') '. $achternaam);
		} else {
			return urldecode($voorletters .' '. $achternaam);
		}
	}
}


function sendMail($ontvanger, $subject, $bericht, $var) {
	global $ScriptURL, $ScriptMailAdress, $ScriptTitle, $SubjectPrefix;
	
	$UserData = getMemberDetails($ontvanger);
		
	$HTMLHeader	= '<html>'.NL;
	$HTMLHeader	.= '<head>'.NL;
	$HTMLHeader	.= '<style type="text/css">'.NL;
	$HTMLHeader	.= 'body		{ background-color:#F2F2F2; font-family:Arial; color:#34383D; }'.NL;
	$HTMLHeader	.= 'p { margin-top: 30px;}'.NL;
	$HTMLHeader	.= '.seperator	{ border-bottom:1px solid #34383D; }'.NL;
	$HTMLHeader	.= '.onderwerp	{ color:#34383D; font-size:24px; font-weight:bold;}'.NL;
	$HTMLHeader	.= '</style>'.NL;
	$HTMLHeader	.= '</head>'.NL;
	$HTMLHeader	.= '<body>'.NL;
	$HTMLHeader	.= '<table width="700" cellpadding="0" cellspacing="0" align="center" bgcolor="ffffff">'.NL;
	$HTMLHeader	.= '	<tr>'.NL;
	$HTMLHeader	.= '		<td colspan="2" height="20" bgcolor="#34383D">&nbsp;</td>'.NL;
	$HTMLHeader	.= '	</tr>'.NL;
	$HTMLHeader	.= '	<tr>'.NL;
	$HTMLHeader	.= '		<td colspan="2" height="10">&nbsp;</td>'.NL;
	$HTMLHeader	.= '	</tr>'.NL;
	$HTMLHeader	.= '    <tr>'.NL;
	$HTMLHeader	.= '		<td>'.NL;
	$HTMLHeader	.= '		<table width="630" cellpadding="0" cellspacing="0" align="center" bgcolor="#ffffff">'.NL;
	$HTMLHeader	.= '		<tr>'.NL;
	$HTMLHeader	.= '			<td width="150">&nbsp;</td>'.NL;
	$HTMLHeader	.= '      <td>'.NL;
	$HTMLHeader	.= '			<table cellspacing="0" cellpadding="0">'.NL;
	$HTMLHeader	.= '			<tr>'.NL;
	$HTMLHeader	.= '				<td class="onderwerp" align="left" height="80" valign="bottom">SAMENWERKINGSGEMEENTE CGK-GKV-NGK DEVENTER</td>'.NL;
	$HTMLHeader	.= '			</tr>'.NL;
	$HTMLHeader	.= '			</table>'.NL;
	$HTMLHeader	.= '      </td>'.NL;
	$HTMLHeader	.= '		</tr>'.NL;
	$HTMLHeader	.= '    </table>'.NL;
	$HTMLHeader	.= '    <table width="630" align="center">'.NL;
	$HTMLHeader	.= '			<tr>'.NL;
	$HTMLHeader	.= '				<td colspan="2" class="seperator">&nbsp;</td>'.NL;
	$HTMLHeader	.= '			</tr>'.NL;
	$HTMLHeader	.= '			<tr>'.NL;
	$HTMLHeader	.= '				<td colspan="2">&nbsp;</td>'.NL;
	$HTMLHeader	.= '			</tr>'.NL;
	$HTMLHeader	.= '			<tr>'.NL;
	$HTMLHeader	.= '				<td colspan="2">'.NL;
	
	$HTMLFooter	= '</td>'.NL;
	$HTMLFooter	.= '</tr>'.NL;
	$HTMLFooter	.= '		<tr>'.NL;
	$HTMLFooter	.= '			<td colspan="2" class="seperator">&nbsp;</td>'.NL;
	$HTMLFooter	.= '		</tr>'.NL;
	$HTMLFooter	.= '		<tr>'.NL;
	$HTMLFooter	.= '			<td colspan="2">&nbsp;</td>'.NL;
	$HTMLFooter	.= '		</tr>'.NL;
	$HTMLFooter	.= '    </table>'.NL;
	$HTMLFooter	.= '		</td>'.NL;
	$HTMLFooter	.= '	</tr>'.NL;
	$HTMLFooter	.= '	<tr>'.NL;
	$HTMLFooter	.= '		<td colspan="2" height="20" bgcolor="#34383D">&nbsp;</td>'.NL;
	$HTMLFooter	.= '	</tr>'.NL;
	$HTMLFooter	.= '</table>'.NL;
	$HTMLFooter	.= '</table>'.NL;
	$HTMLFooter	.= '<br /><br /><br /><br /><br /><br />'.NL;
	$HTMLFooter	.= '</body>'.NL;
	$HTMLFooter	.= '</html>'.NL;
					
	$HTMLMail = $HTMLHeader.$bericht.$HTMLFooter;
		
	$mail = new PHPMailer;
	
	if($var['from'] != "") {
		$mail->From     = $var['from'];
	} else {
		$mail->From     = $ScriptMailAdress;
	}
	
	if($var['FromName'] != "") {
		$mail->FromName = $var['FromName'];
	} else {
		$mail->FromName = $ScriptTitle;
	}
			
	$mail->AddAddress($UserData['mail'], makeName($ontvanger, 5));	
	//$mail->AddBCC('internet@draijer.org');
	$mail->Subject	= $SubjectPrefix . $subject;
	$mail->IsHTML(true);
	$mail->Body			= $HTMLMail;
	//$mail->AltBody	= $PlainMail;
	
	if($var['file'] != "") {
		if($var['name'] != "") {
			$mail->addAttachment($var['file'], $var['name']);
		} else {
			$mail->addAttachment($var['file']);
		}
	}
	
	if(!$mail->Send()) {
		return false;
	} else {
		return true;
	}
}


function showBlock($block, $width) {
	$HTML[] = "<table width='$width%' cellpadding='8' cellspacing='1' bgcolor='#d2d2d2'>";
	$HTML[] = "<tr>                                                                 ";
	$HTML[] = "	<td bgcolor='#ffffff'>$block</td>";
	$HTML[] = "</tr>";
	$HTML[] = "</table>";
		
	return implode(NL, $HTML);
}

function getFamilieleden($id) {
	global $TableUsers, $UserAdres, $UserID;
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT $UserID FROM $TableUsers WHERE $UserAdres IN (SELECT $UserAdres FROM $TableUsers WHERE $UserID = $id)";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$UserID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function getJarigen($dag, $maand) {
	global $TableUsers, $UserID, $UserGeboorte;
	$db = connect_db();
	
	$data = array();
	
	$sql = "SELECT $UserID FROM $TableUsers WHERE DAYOFMONTH($UserGeboorte) = $dag AND MONTH($UserGeboorte) = $maand";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$data[] = $row[$UserID];
		} while($row = mysqli_fetch_array($result));		
	}
	return $data;	
}

function toLog($type, $dader, $slachtoffer, $message) {
	global $db,$TableLog, $LogID, $LogTime, $LogType, $LogUser, $LogSubject, $LogMessage;	

	connect_db();
 	
	$tijd = time();	
	$sql = "INSERT INTO $TableLog ($LogTime, $LogType, $LogUser, $LogSubject, $LogMessage) VALUES ($tijd, '$type', '$dader', '$slachtoffer', '". addslashes($message) ."')";
	if(!mysqli_query($db, $sql)) {
		echo "log-error : ". $sql;
	}
}

function getParam($name, $default = '') {
	return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}


?>