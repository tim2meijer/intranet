<?php
include_once('include/functions.php');
include_once('include/config.php');

$db = connect_db();

$header[] = "BEGIN:VCALENDAR";
$header[] = "VERSION:2.0";
//$header[] = "X-WR-CALDESC:3GK-gebedspunten.";
$header[] = "PRODID:-//hacksw/handcal//NONSGML v1.0//EN";
$header[] = "BEGIN:VTIMEZONE";
$header[] = "TZID:Europe/Amsterdam";
$header[] = "X-LIC-LOCATION:Europe/Amsterdam";
$header[] = "BEGIN:DAYLIGHT";
$header[] = "TZOFFSETFROM:+0100";
$header[] = "TZOFFSETTO:+0200";
$header[] = "TZNAME:CEST";
$header[] = "DTSTART:19700329T020000";
$header[] = "RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU";
$header[] = "END:DAYLIGHT";
$header[] = "BEGIN:STANDARD";
$header[] = "TZOFFSETFROM:+0200";
$header[] = "TZOFFSETTO:+0100";
$header[] = "TZNAME:CET";
$header[] = "DTSTART:19701025T030000";
$header[] = "RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU";
$header[] = "END:STANDARD";
$header[] = "END:VTIMEZONE";

$footer[] = "END:VCALENDAR";


$ics = array();

$sql_rooster = "SELECT $TablePlanning.$PlanningDienst FROM $TablePlanning, $TableDiensten WHERE $TablePlanning.$PlanningDienst = $TableDiensten.$DienstID AND $TableDiensten.$DienstEind > ". time() ." GROUP BY $PlanningDienst";
$result_rooster = mysqli_query($db, $sql_rooster);
if($row_rooster = mysqli_fetch_array($result_rooster)) {		
	do {
		$dienst = $row_rooster[$PlanningDienst];
		$data_dienst = getKerkdienstDetails($dienst);

		$sql_dienst = "SELECT * FROM $TablePlanning WHERE $PlanningDienst = $dienst GROUP BY $PlanningGroup";
		$result_dienst = mysqli_query($db, $sql_dienst);
		if($row_dienst = mysqli_fetch_array($result_dienst)) {
			$start = $data_dienst['start'];
			$einde = $data_dienst['eind'];

			$ics[] = "BEGIN:VEVENT";	
			$ics[] = "UID:3GK-". substr('00'.$dienst, -3) .'.'. substr('00'.$rooster, -3);
			$ics[] = "DTSTART;TZID=Europe/Amsterdam:". date("Ymd\THis", $start);
			$ics[] = "DTEND;TZID=Europe/Amsterdam:". date("Ymd\THis", $einde);	
			$ics[] = "LAST-MODIFIED:". date("Ymd\THis", time());
			
			if(date("H", $start) < 12) {
				$ics[] = "SUMMARY:Ochtenddienst";
			} elseif(date("H", $start) < 18) {
				$ics[] = "SUMMARY:Middagdienst";
			} else {
				$ics[] = "SUMMARY:Kerkdienst";
			}
			
			$string = '';
		
			do {
				$rooster = $row_dienst[$PlanningGroup];			
				$data_rooster = getRoosterDetails($rooster);
			
				$sql_persoon = "SELECT * FROM $TablePlanning WHERE $PlanningGroup = $rooster AND $PlanningDienst = $dienst";
				$result_persoon = mysqli_query($db, $sql_persoon);
								
				if($row_persoon = mysqli_fetch_array($result_persoon)) {
					$personen = array();
					
					do {
						$personen[] = makeName($row_persoon[$PlanningUser], 5);
					} while($row_persoon = mysqli_fetch_array($result_persoon));
					
					$string .= '<i>'. $data_rooster[$RoostersNaam] .'<\/i>\n'. implode('\n', $personen) .'\n\n';
				}
			} while($row_dienst = mysqli_fetch_array($result_dienst));
			
			$ics[] = "DESCRIPTION:".$string;
			$ics[] = "STATUS:CONFIRMED";	
			$ics[] = "TRANSP:TRANSPARENT";
			$ics[] = "END:VEVENT";				
		}
	} while($row_rooster = mysqli_fetch_array($result_rooster));	
}

$file_name = 'ical/scipio.ics';
	
$file = fopen($file_name, 'w+');
fwrite($file, implode("\r\n", $header));
fwrite($file, "\r\n");
fwrite($file, implode("\r\n", $ics));
fwrite($file, "\r\n");
fwrite($file, implode("\r\n", $footer));
fclose($file);

echo $ScriptURL.'/'.$file_name .'<br>';

//echo implode("\r\n", $ics);

?>