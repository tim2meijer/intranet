<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('../../general_include/class.phpmailer.php');
$db = connect_db();

#$startTijd = mktime(0, 0, 0, date("n"), date("j"), date("Y"));
#$eindTijd = mktime(23, 59, 59, date("n"), (date("j")+7), date("Y"));

$startTijd = mktime(0, 0, 0, date("n"), (date("j")+3), date("Y"));
$eindTijd = mktime(23, 59, 59, date("n"), (date("j")+3), date("Y"));

$diensten = getKerkdiensten($startTijd, $eindTijd);
$roosters = getRoosters(0);

# Mochten er diensten zijn, dan even alle teams opvragen
# Van deze teamID's een naam-array maken ($teamVulling).
# Deze $teamVulling wegschrijven in een array met alle team-vullingen per rooster ($teams)
foreach($diensten as $d) {
	foreach($roosters as $r) {
		$vulling = getRoosterVulling($r, $d);
		
		$teamVulling = array();
		foreach($vulling as $lid) {
			$teamVulling[$lid] = makeName($lid, 5);
		}
		
		$teams[$d][$r] = $teamVulling;
	}
}

# Alle diensten doorlopen
foreach($diensten as $dienst) {
	$dienstData = getKerkdienstDetails($dienst);
	foreach($roosters as $rooster) {
		$vulling = $teams[$dienst][$rooster];
	
		if(count($vulling) > 0) {
			$roosterData			= getRoosterDetails($rooster);			
			$HTMLMail					= $roosterData['text_mail'];
			$onderwerp				= $roosterData['onderwerp_mail'];
			$var['FromName']	= $roosterData['naam_afzender'];
			$var['from']			= $roosterData['mail_afzender'];
															
			foreach($vulling as $lid => $naam) {
				$team = excludeID($vulling, $lid);
				
				for($i=0 ; $i < 2 ; $i++) {
					if($i==0) {
						$ReplacedBericht = $HTMLMail;
					} else {
						$ReplacedBericht = $onderwerp;
					}
					
					$ReplacedBericht = str_replace ('[[voornaam]]', makeName($lid, 1), $ReplacedBericht);
					$ReplacedBericht = str_replace ('[[achternaam]]', makeName($lid, 4), $ReplacedBericht);
					$ReplacedBericht = str_replace ('[[dag]]', strftime ("%A", $dienstData['start']), $ReplacedBericht);
										
					# Als er meer dan 1 teamlid is dan een opsommingslijst, anders gewoon een vermelding
					if(count($team) == 1) {
						$ReplacedBericht = str_replace ('[[team]]', current($team), $ReplacedBericht);
					} elseif(count($team) > 1) {
						$ReplacedBericht = str_replace ('[[team]]', makeOpsomming($team), $ReplacedBericht);
						# str_replace ("[[team]]", "<ul>\n<li>".implode("</li>\n<li>", $team)."</li>\n</ul>", $ReplacedBericht);
					} else {
						$ReplacedBericht = str_replace ('[[team]]', 'onbekend', $ReplacedBericht);
					}
					
					# Als [[team|X]] voorkomt moeten deze vervangen worden
					# Daarvoor worden alle roosters doorlopen en team erbij zoeken ($anderTeam)
					# Als [[team|$roos]] voorkomt wordt dat vervangen door dat team
					if(strpos($ReplacedBericht, '[[team|')) {
						foreach($roosters as $roos) {
							$anderTeam = $teams[$dienst][$roos];												
							
							if(count($anderTeam) == 1) {
								$ReplacedBericht = str_replace ("[[team|$roos]]", current($anderTeam), $ReplacedBericht);
							} elseif(count($anderTeam) > 1) {
								$ReplacedBericht = str_replace ("[[team|$roos]]", makeOpsomming($anderTeam), $ReplacedBericht);
							} else {
								$ReplacedBericht = str_replace ("[[team|$roos]]", 'onbekend', $ReplacedBericht);
							}
						}
					}
										
					if($i==0) {
						$memberData = getMemberDetails($lid);
						$ReplacedBericht .= "<p>Ps 1. : mocht je onderling geruild hebben, wil je deze mail dan doorsturen naar de betreffende persoon?<br>In het vervolg kan je die ruiling ook doorgeven via <a href='$ScriptURL/showRooster.php?rooster=$rooster'>het rooster</a> zelf, dan komt de mail direct goed terecht.";	
						$ReplacedBericht .= "<br>Ps 2. : je kan je persoonlijke 3GK-rooster opnemen in je digitale agenda door <a href='$ScriptURL/ical/".$memberData['username'].'-'. $memberData['hash'] .".ics'>deze link</a> toe te voegen.";
						
						$FinalHTMLMail = $ReplacedBericht;
					} else {
						$FinalSubject = $ReplacedBericht;
					}					
				}

				if(sendMail($lid, $FinalSubject, $FinalHTMLMail, $var)) {
					toLog('debug', '', $lid, 'reminder-mail '. $roosterData['naam'] .' verstuurd');
				} else {
					toLog('error', '', $lid, 'problemen met reminder-mail '. $roosterData['naam'] .' versturen');
				}
			}
		}
	}
}

?>