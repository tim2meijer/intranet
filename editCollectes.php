<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
include_once('include/HTML_HeaderFooter.php');
include_once('../../general_include/class.phpmailer.php');

$db = connect_db();
$cfgProgDir = 'auth/';
$requiredUserGroups = array(1, 22);
include($cfgProgDir. "secure.php");

# Als er op een knop gedrukt is, het rooster wegschrijven
if(isset($_POST['save']) OR isset($_POST['maanden'])) {	
	foreach($_POST['collecte'] as $dienst => $collectes) {
		$oldData = getKerkdienstDetails($dienst);
		
		$set = array();
		$set[] = $DienstCollecte_1 .' = \''. urlencode($collectes[1]) .'\'';
		$set[] = $DienstCollecte_2 .' = \''. urlencode($collectes[2]) .'\'';
		
		$sql = "UPDATE $TableDiensten SET ". implode(', ', $set)." WHERE $DienstID = ". $dienst;		
			
		if(mysqli_query($db, $sql)) {
			if($oldData['collecte_1'] != '' AND $oldData['collecte_1'] != $collectes[1]) {
				$bericht[] = '1ste collecte van '. date('d-m-y', $oldData['start']).' is gewijzigd van <i>'. $oldData['collecte_1'] .'</i> naar <i>'. $collectes[1] .'</i>';
			}
			
			if($oldData['collecte_2'] != '' AND $oldData['collecte_2'] != $collectes[2]) {
				$bericht[] = '2de collecte van '. date('d-m-y', $oldData['start']).' is gewijzigd van <i>'. $oldData['collecte_2'] .'</i> naar <i>'. $collectes[2] .'</i>';
			}
		}
	}
	
	if(isset($bericht)){
		$HTMLMail = $MailHeader.implode('<br>', $bericht).$MailFooter;
		
		$mail = new PHPMailer;
	
		$mail->From     = $ScriptMailAdress;
		$mail->FromName = $ScriptTitle;
		$mail->AddAddress('scipiobeheer@koningskerkdeventer.nl', 'Scipio beheer');
	
		$mail->Subject	= $SubjectPrefix . count($subject).' '.(count($subject) > 1 ? 'gewijzigde collectedoelen' : 'gewijzigd collectedoel');
		$mail->IsHTML(true);
		$mail->Body			= $HTMLMail;
		
		if(!$mail->Send()) {
			toLog('error', $_SESSION['ID'], '', 'Kon geen mail sturen naar Scipio-beheer voor gewijzigde collectes');
		} else {
			toLog('debug', $_SESSION['ID'], '', 'Mail gestuurd naar Scipio-beheer voor gewijzigde collectes');
		}		
	}
	
	toLog('info', $_SESSION['ID'], '', 'Collectes bijgewerkt');
}

# Als er op de knop van 3 maanden extra geklikt is, 3 maanden bij de eindtijd toevoegen
# Eerst initeren, event. later ophogen
if(isset($_POST['blokken'])) {
	$blokken = $_POST['blokken'];
} else {
	$blokken = 1;
}

if(isset($_POST['maanden'])) {
	$blokken++;
}

# Haal alle kerkdiensten binnen een tijdsvak op
$diensten = getKerkdiensten(mktime(0,0,0), mktime(date("H"),date("i"),date("s"),(date("n")+(3*$blokken))));

//$text[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";
$text[] = "<form method='post' action='$_SERVER[PHP_SELF]'>";

$text[] = "<input type='hidden' name='blokken' value='$blokken'>";
$text[] = "<table>";
$text[] = "<tr>";
$text[] = "	<td>Datum</td>";
//$text[] = "	<td>Datum</td>";
$text[] = "	<td>Start</td>";
$text[] = "	<td>Collecte 1</td>";
$text[] = "	<td>Collecte 2</td>";
$text[] = "	<td>Bijzonderheid</td>";
$text[] = "</tr>";

foreach($diensten as $dienst) {
	$data = getKerkdienstDetails($dienst);
	
	$text[] = "<tr>";
	$text[] = "	<td align='right'>". strftime("%a %e %b", $data['start']) ."</td>";
	$text[] = "	<td>". date('H:i', $data['start']) ."</td>";	
	$text[] = "	<td><input type='text' name='collecte[$dienst][1]' value='". addslashes($data['collecte_1']) ."'></td>";
	$text[] = "	<td><input type='text' name='collecte[$dienst][2]' value='". addslashes($data['collecte_2']) ."'></td>";		
	$text[] = "	<td>". $data['bijzonderheden'] ."</td>";
	$text[] = "</tr>";
}

$text[] = "<tr>";
$text[] = "<td colspan='5' align='middle'><input type='submit' name='save' value='Diensten opslaan'>&nbsp;<input type='submit' name='maanden' value='Volgende 3 maanden'></td>";
$text[] = "</tr>";
$text[] = "</table>";
$text[] = "</form>";

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>
