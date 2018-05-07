<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
include_once('../../general_include/class.phpmailer.php');
include_once('../../general_include/class.html2text.php');
$requiredUserGroups = array(1);
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(isset($_POST['send_mail'])) {
	$lid = $_POST['ontvanger'];
	$memberData = getMemberDetails($lid);
	
	for($i=0 ; $i < 2 ; $i++) {
		if($i==0) {
			$ReplacedBericht = $_POST['text_mail'];
		} else {
			$ReplacedBericht = $_POST['onderwerp_mail'];
		}
		
		$ReplacedBericht = str_replace ('[[voornaam]]', $memberData['voornaam'], $ReplacedBericht);
		$ReplacedBericht = str_replace ('[[achternaam]]', $memberData['achternaam'], $ReplacedBericht);
		$ReplacedBericht = str_replace ('[[hash_kort]]', $memberData['hash_short'], $ReplacedBericht);
		$ReplacedBericht = str_replace ('[[hash_lang]]', $memberData['hash_long'], $ReplacedBericht);
		
		if($i==0) {								
			$FinalHTMLMail = $ReplacedBericht;
		} else {
			$FinalSubject = $ReplacedBericht;
		}					
	}
	
	if(sendMail($lid, $FinalSubject, $FinalHTMLMail, $var)) {
		toLog('debug', '', $lid, "Mail met als onderwerp '$FinalSubject' verstuurd");
	} else {
		toLog('error', '', $lid, "Problemen met versturen mail met onderwerp '$FinalSubject'");
	}	
} else {
	$leden = getMembers();
	
	$block[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
	$block[] = "<table>";
	$block[] = "<tr>";
	$block[] = "	<td valign='top'>Ontvanger</td>";
	$block[] = "	<td valign='top' colspan='2'><select name='ontvanger'>";
	foreach($leden as $lid)	$block[] = "<option value='$lid'>". makeName($lid, 8) ."</option>";	
	$block[] = "	</select></td>";
	$block[] = "</tr>";
	$block[] = "<tr>";
	$block[] = "	<td valign='top'>Afzendernaam</td>";
	$block[] = "	<td valign='top' colspan='2'><input type='text' name='naam_afzender' size=80 value='$ScriptTitle'></td>";
	$block[] = "</tr>";
	$block[] = "<tr>";
	$block[] = "	<td valign='top'>Afzenderadres</td>";
	$block[] = "	<td valign='top' colspan='2'><input type='text' name='mail_afzender' size=80 value='$ScriptMailAdress'></td>";
	$block[] = "</tr>";
	$block[] = "<tr>";
	$block[] = "	<td valign='top'>Onderwerp</td>";
	$block[] = "	<td valign='top' colspan='2'><input type='text' name='onderwerp_mail' size=80 value=''></td>";
	$block[] = "</tr>";
	$block[] = "<tr>";
	$block[] = "	<td valign='top'>Mailtekst</td>";
	$block[] = "	<td valign='top'><textarea name='text_mail' rows=20 cols=60></textarea></td>";
	$block[] = "	<td valign='top'>[[voornaam]] = voornaam van de ontvanger<br>";
	$block[] = "	[[achternaam]] = achternaam van de ontvanger<br>";
	$block[] = "	[[hash_kort]] = korte hash<br>";
	$block[] = "	[[hash_lang]] = lange hash</td>";
	$block[] = "</tr>";
	$block[] = "<tr>";
	$block[] = "	<td valign='top'>&nbsp;</td><td valign='top' colspan='2'><input type='submit' name='send_mail' value='Mail versturen'></td>";
	$block[] = "</tr>";
	$block[] = "</table>";
	$block[] = "</form>";
	
	echo $HTMLHeader;
	echo showBlock(implode(NL, $block), 100);
	echo $HTMLFooter;	
}

?>