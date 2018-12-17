<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_HeaderFooter.php');
include_once('../../general_include/class.phpmailer.php');
include_once('../../general_include/class.html2text.php');
$db = connect_db();

$startTijd	= mktime(0, 0, 0, date("n"), (date("j")+18), date("Y"));
$eindTijd		= mktime(23, 59, 59, date("n"), (date("j")+18), date("Y"));
$diensten		= getKerkdiensten($startTijd, $eindTijd);

foreach($diensten as $dienst) {
	$dienstData			= getKerkdienstDetails($dienst);
	$voorgangerData = getVoorgangerData($dienstData['voorganger_id']);
	
	$aBandleider		= getRoosterVulling(22, $dienst);
	$bandleider			= $aBandleider[0];
	$bandData				= getMemberDetails($bandleider);
	
	$aSchriftlezer	= getRoosterVulling(12, $dienst);
	$schriftlezer		= $aSchriftlezer[0];
	$schriftData		= getMemberDetails($schriftlezer);
	
	if(date("H", $dienstData['start']) < 12) {
		$dagdeel = 'morgendienst';
	} elseif(date("H", $dienstData['start']) < 18) {
		$dagdeel = 'middagdienst';
	} else {
		$dagdeel = 'avonddienst';
	}
	
	# Achternaam
	$voorgangerAchterNaam = '';
	if($voorgangerData['tussen'] == '')	$voorgangerAchterNaam = lcfirst($voorgangerData['tussen']).' ';	
	$voorgangerAchterNaam .= $voorgangerData['achter'];
	
	if($voorgangerData['voor'] != "") {
		$aanspeekNaam = $voorgangerData['voor'];
		$mailNaam = $voorgangerData['voor'].' '.$voorgangerAchterNaam;
	} else {
		$aanspeekNaam = lcfirst($voorgangerData['titel']).' '.$voorgangerAchterNaam;
		$mailNaam = $voorgangerData['init'].' '.$voorgangerAchterNaam;
	}
				
	$mail = array(); 
	$mail[] = "Beste $aanspeekNaam,";
	$mail[] = "";
	$mail[] = "Fijn dat u komt preken in de $dagdeel van ". strftime ('%e %B', $dienstData['start'])." om ". date('H:i', $dienstData['start'])." uur, in de Koningskerk te Deventer.";
	$mail[] = "Ik geef u de nodige informatie door.";
	$mail[] = "";
	$mail[] = "De band wordt verzorgd door ". makeName($bandleider, 5) .".";
	$mail[] = "Schriftlezing door ". makeName($schriftlezer, 5) .".";
	$mail[] = "";
	$mail[] = "U kunt de liturgie afstemmen met ". makeName($bandleider, 1) ." voor de muziek. ". ($bandData['geslacht'] == 'M' ? 'Hij' : 'Zij') ." kan dan aangeven of liederen bekend en of geschikt zijn in onze gemeente en eventuele suggesties voor een vervangend lied.";
	$mail[] = "Wilt u de liturgie een week van te voren doorgeven zodat de band kan oefenen.";
	$mail[] = "Als u deze mail beantwoordt met \"allen\" dan is iedereen op tijd op de hoogte.";
	$mail[] = "";
	$mail[] = "In de bijlage treft u de aandachtspunten van de dienst en het declaratieformulier aan.";
	$mail[] = "";
	$mail[] = "Mochten er nog vragen zijn dan hoor ik het graag.";
	$mail[] = "";
	$mail[] = "Vriendelijke groeten";
	$mail[] = "";
	$mail[] = "Jenny van der Vegt-Huzen";
	$mail[] = "Tel.: 06-10638291";
	$mail[] = "jenny@overbrugger.nl";
	
	$Subject = "Preken $dagdeel ". date('j-n-Y', $dienstData['start']);
	
	$HTMLMail = $MailHeader.implode("<br>\n", $mail).$MailFooter;	
	$html =& new html2text($HTMLMail);
	$html->set_base_url($ScriptURL);
	$PlainMail = $html->get_text();
	
	$mail = new PHPMailer;
	$mail->FromName	= "Jenny van der Vegt-Huzen";
	$mail->From			= "jenny@overbrugger.nl";
	
	/*
	$mail->AddAddress($voorgangerData['mail'], $mailNaam);
	$mail->AddCC($bandData['mail'], makeName($bandleider, 5));
	$mail->AddCC($schriftData['mail'], makeName($schriftlezer, 5));
	$mail->AddCC("Beamteam 3GK", "beamteam3gk@gmail.com");
	$mail->AddCC("Mededelingen 3GK", "mededelingen@3gk-deventer.nl");
	$mail->AddBCC("Jenny van der Vegt-Huzen", "jenny@overbrugger.nl");
	$mail->AddBCC('internet@draijer.org');
	*/
		
	$mail->AddAttachment('download/aandachtspunten.pdf', 'Aandachtspunten Liturgie Deventer (dd 11-6-2018).pdf');
	$mail->AddAttachment('download/declaratieformulier.xlsx', date('ymd', $dienstData['start'])."_declaratieformulier_". str_replace(' ', '', $voorgangerAchterNaam) .".xlsx");
	
	$mail->AddAddress('matthijs@draijer.org', $mailNaam);
	
	$mail->Subject	= trim($Subject);
	$mail->IsHTML(true);
	$mail->Body			= $HTMLMail;
	$mail->AltBody	= $PlainMail;
	
	if(!$mail->Send()) {
		toLog('error', '', '', 'Problemen met voorgangersmail versturen');
	} else {
		toLog('info', '', '', 'Voorgangersmail verstuurd');
	}
	
	/*
	echo $voorgangerData['mail'] .' - > '. $mailNaam .'<br>';
	echo $bandData['mail'] .' - > '. makeName($bandleider, 5) .'<br>';
	echo $schriftData['mail'] .' - > '. makeName($schriftlezer, 5) .'<br>';

	echo $HTMLMail;
	*/
}
