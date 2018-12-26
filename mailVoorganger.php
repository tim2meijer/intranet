<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_HeaderFooter.php');
include_once('../../general_include/class.phpmailer.php');
include_once('../../general_include/class.html2text.php');
$db = connect_db();

# Omdat de server deze dagelijks moet draaien wordt toegang niet gedaan op basis
# van naam+wachtwoord maar op basis van IP-adres
if(in_array($_SERVER['REMOTE_ADDR'], $allowedIP)) {
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
		if($voorgangerData['tussen'] != '')	$voorgangerAchterNaam = lcfirst($voorgangerData['tussen']).' ';	
		$voorgangerAchterNaam .= $voorgangerData['achter'];
		
		if($voorgangerData['voor'] != "") {
			$aanspeekNaam = $voorgangerData['voor'];
			$mailNaam = $voorgangerData['voor'].' '.$voorgangerAchterNaam;
		} else {
			$aanspeekNaam = lcfirst($voorgangerData['titel']).' '.$voorgangerAchterNaam;
			$mailNaam = $voorgangerData['init'].' '.$voorgangerAchterNaam;
		}
		
		# Nieuw mail-object aanmaken
		$mail = new PHPMailer;
		$mail->FromName	= 'Preekvoorziening Koningskerk Deventer';
		$mail->From			= $ScriptMailAdress;
		$mail->AddReplyTo('jenny@overbrugger.nl', 'Jenny van der Vegt-Huzen');
		
		# Alle geadresseerden toevoegen
		$mail->AddAddress($voorgangerData['mail'], $mailNaam);
		$mail->AddCC($bandData['mail'], makeName($bandleider, 6));
		$mail->AddCC($schriftData['mail'], makeName($schriftlezer, 6));
		$mail->AddCC('beamteam3gk@gmail.com', 'Beamteam 3GK');
		$mail->AddCC('mededelingen@3gk-deventer.nl', 'Mededelingen 3GK');
		$mail->AddCC('nieuwesite@3gk-deventer.nl','Webmaster 3GK');
		$mail->AddBCC('jenny@overbrugger.nl');
		$mail->AddBCC('internet@draijer.org');
		$mail->AddBCC('matthijs.draijer@koningskerkdeventer.nl');
		
		//$mail->AddAddress('matthijs@draijer.org', $mailNaam);
		
		# Mail opstellen
		$mailText = array(); 
		$mailText[] = "Beste $aanspeekNaam,";
		$mailText[] = "";
		$mailText[] = "Fijn dat u komt preken in de $dagdeel van ". strftime ('%e %B', $dienstData['start'])." om ". date('H:i', $dienstData['start'])." uur, in de Koningskerk te Deventer.";
		$mailText[] = "Ik geef u de nodige informatie door.";
		$mailText[] = "";
		$mailText[] = "De band wordt geleid door ". makeName($bandleider, 5) .".";
		$mailText[] = "Schriftlezing door ". makeName($schriftlezer, 5) .".";
		$mailText[] = "";
		$mailText[] = "U kunt de liturgie afstemmen met ". makeName($bandleider, 1) ." voor de muziek. ". ($bandData['geslacht'] == 'M' ? 'Hij' : 'Zij') ." kan dan aangeven of liederen bekend en of geschikt zijn in onze gemeente en eventuele suggesties voor een vervangend lied.";
		$mailText[] = "Wilt u de liturgie een week van te voren doorgeven zodat de band kan oefenen.";
		$mailText[] = "Als u deze mail beantwoordt met \"allen\" dan is iedereen op tijd op de hoogte.";
		
		# Bij gast-predikanten (niet zijnde Evert en Wim) moeten er bijlages bij
		if($dienstData['voorganger_id'] != 15 AND $dienstData['voorganger_id'] != 52) {
			$mailText[] = "";
			$mailText[] = "In de bijlage treft u de aandachtspunten van de dienst en het declaratieformulier aan.";
					
			$mail->AddAttachment('download/aandachtspunten.pdf', 'Aandachtspunten Liturgie Deventer (dd 11-6-2018).pdf');
			$mail->AddAttachment('download/declaratieformulier.xlsx', date('ymd', $dienstData['start'])."_Declaratieformulier_". str_replace(' ', '', $voorgangerAchterNaam) .".xlsx");	
		}
		
		$mailText[] = "";
		$mailText[] = "Mochten er nog vragen zijn dan hoor ik het graag.";
		$mailText[] = "";
		$mailText[] = "Vriendelijke groeten";
		$mailText[] = "";
		$mailText[] = "Jenny van der Vegt-Huzen";
		$mailText[] = "Tel.: 06-10638291";
		$mailText[] = "jenny@overbrugger.nl";
		
		# Onderwerp maken
		$Subject = "Preken $dagdeel ". date('j-n-Y', $dienstData['start']);
		
		# HTML- en plaintext-mail maken en deze toekennen aan het mail-object
		$HTMLMail = $MailHeader.implode("<br>\n", $mailText).$MailFooter;	
		$html =& new html2text($HTMLMail);
		$html->set_base_url($ScriptURL);
		$PlainMail = $html->get_text();
		
		$mail->Subject	= trim($Subject);
		$mail->IsHTML(true);
		$mail->Body	= $HTMLMail;
		$mail->AltBody	= $PlainMail;
		
		if(!$mail->Send()) {
			toLog('error', '', '', "Problemen met voorgangersmail versturen naar $mailNaam voor ". date('j-n-Y', $dienstData['start']));
			echo "Problemen met mail versturen<br>\n";
		} else {
			toLog('info', '', '', "Voorgangersmail verstuurd naar $mailNaam voor ". date('j-n-Y', $dienstData['start']));
			echo "Mail verstuurd naar $mailNaam<br>\n";
		}
	}
} else {
	toLog('error', '', 'Poging handmatige run vorgangermail, IP:'.$_SERVER['REMOTE_ADDR']);
}
?>