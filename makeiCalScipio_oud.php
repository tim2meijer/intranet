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

if(isset($_REQUEST['rooster'])) {
	$roosters[] = $_REQUEST['rooster'];
} else {
	$sql = "SELECT * FROM $TablePlanning GROUP BY $PlanningGroup";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$roosters[] = $row[$PlanningGroup];
		} while($row = mysqli_fetch_array($result));
	}	
}

foreach($roosters as $rooster) {
	$ics = array();
	$data_rooster = getRoosterDetails($rooster);
		
	$sql_rooster = "SELECT * FROM $TablePlanning WHERE $PlanningGroup = $rooster GROUP BY $PlanningDienst";
	$result_rooster = mysqli_query($db, $sql_rooster);
	if($row_rooster = mysqli_fetch_array($result_rooster)) {		
		do {
			$dienst = $row_rooster[$PlanningDienst];
			$data_dienst = getKerkdienstDetails($dienst);
				
			$start = $data_dienst['start'];
			$einde = $data_dienst['eind'];
				
			$sql_dienst = "SELECT * FROM $TablePlanning WHERE $PlanningGroup = $rooster AND $PlanningDienst = $dienst";
			$result_dienst = mysqli_query($db, $sql_dienst);
						
			if($row_dienst = mysqli_fetch_array($result_dienst)) {
				$ics[] = "BEGIN:VEVENT";	
				$ics[] = "UID:3GK-". substr('00'.$dienst, -3) .'.'. substr('00'.$rooster, -3);
				$ics[] = "DTSTART;TZID=Europe/Amsterdam:". date("Ymd\THis", $start);
				$ics[] = "DTEND;TZID=Europe/Amsterdam:". date("Ymd\THis", $einde);	
				$ics[] = "LAST-MODIFIED:". date("Ymd\THis", time());
				$ics[] = "SUMMARY:". $data_rooster[$RoostersNaam];
				//$ics[] = "LOCATION:". convertToReadable($data['adres']) .", ". $data['plaats'];
				
				$personen = array();
				
				do {
					$personen[] = makeName($row_dienst[$PlanningUser], 5);
				} while($row_dienst = mysqli_fetch_array($result_dienst));
		
				$ics[] = 'DESCRIPTION:Op het rooster staan\n'. implode('\n', $personen);
				$ics[] = "STATUS:CONFIRMED";	
				$ics[] = "TRANSP:TRANSPARENT";
				$ics[] = "END:VEVENT";		
			}
		} while($row_rooster = mysqli_fetch_array($result_rooster));	
	}
	
	$file_name = 'ical/'. $data_rooster[$RoostersNaam] .'.ics';
	
	$file = fopen($file_name, 'w+');
	fwrite($file, implode("\r\n", $header));
	fwrite($file, "\r\n");
	fwrite($file, implode("\r\n", $ics));
	fwrite($file, "\r\n");
	fwrite($file, implode("\r\n", $footer));
	fclose($file);
	
	echo $ScriptURL.'/'.$file_name .'<br>';
	
}
?>