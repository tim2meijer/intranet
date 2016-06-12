<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('../../general_include/class.phpmailer.php');
#include_once('include/HTML_TopBottom.php');
//$cfgProgDir = 'auth/';
//include($cfgProgDir. "secure.php");
$db = connect_db();

//$dienst = getNextDienst();

$startTijd = mktime(0, 0, 0, date("n"), (date("j")+3), date("Y"));
$eindTijd = mktime(23, 59, 59, date("n"), (date("j")+3), date("Y"));

//$diensten = getKerkdiensten($startTijd, $eindTijd);
$diensten = array(5);
$roosters = getRoosters(0);

foreach($diensten as $dienst) {
	$dienstData = getKerkdienstDetails($dienst);
	foreach($roosters as $rooster) {
		$vulling = getRoosterVulling($rooster, $dienst);
	
		if(count($vulling) > 0) {
			$roosterData = getRoosterDetails($rooster);			
			$HTMLMail					= $roosterData['text_mail'];
			$onderwerp				= $roosterData['onderwerp_mail'];
			$var['FromName']	= $roosterData['naam_afzender'];
			$var['from']			= $roosterData['mail_afzender'];
									
			foreach($vulling as $lid) {
				$team = array();
				foreach($vulling as $teamLid) {
					if($teamLid != $lid) {
						$team[] = makeName($teamLid, 5);
					}
				}
				
				for($i=0 ; $i < 2 ; $i++) {
					if($i==0) {
						$ReplacedBericht = $HTMLMail;
					} else {
						$ReplacedBericht = $onderwerp;
					}
					
					$ReplacedBericht = str_replace ('[[voornaam]]', makeName($lid, 1), $ReplacedBericht);
					$ReplacedBericht = str_replace ('[[achternaam]]', makeName($lid, 4), $ReplacedBericht);
					$ReplacedBericht = str_replace ('[[dag]]', strftime ("%A", $dienstData['start']), $ReplacedBericht);
					$ReplacedBericht = str_replace ('[[team]]', "<ul>\n<li>".implode("</li>\n<li>", $team)."</li>\n</ul>", $ReplacedBericht);
					
					if($i==0) {
						$FinalHTMLMail = $ReplacedBericht;
					} else {
						$FinalSubject = $ReplacedBericht;
					}					
				}
								
				sendMail($lid, $FinalSubject, $FinalHTMLMail, $var);
			}
		}
	}
}

?>