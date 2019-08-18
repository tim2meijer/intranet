<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_HeaderFooter.php');
include_once('../../../general_include/class.phpmailer.php');
#$requiredUserGroups = array(1);
#$cfgProgDir = '../auth/';
#include($cfgProgDir. "secure.php");
$db = connect_db();

$roosters = getRoosters();

foreach($roosters as $rooster) {
	$roosterData			= getRoosterDetails($rooster);
	
	if($roosterData['alert'] > 0) {		
		$sql = "SELECT * FROM $TableDiensten, $TablePlanning WHERE $TablePlanning.$PlanningDienst = $TableDiensten.$DienstID AND $TablePlanning.$PlanningGroup = $rooster AND $TableDiensten.$DienstStart > ". (time()+($roosterData['alert']*7*24*60*60));
		$result = mysqli_query($db, $sql);
		
		if(mysqli_num_rows($result) == 0) {
			$alert[] = "<a href='". $ScriptSever ."/showRooster.php?rooster=$rooster'>". $roosterData['naam']. '</a> heeft minder dan '. $roosterData['alert'] ." weken vulling (<a href='". $ScriptSever ."/admin/editRoosters.php?id=$rooster'>edit</a>)";
		}
	}	
}

if(is_array($alert)) {
	$HTMLMail = $MailHeader.implode("<br>\n", $alert).$MailFooter;
	
	$mail = new PHPMailer;
	
	$mail->From     = $ScriptMailAdress;
	$mail->FromName = $ScriptTitle;
	$mail->AddAddress('internet@draijer.org');
	
	$mail->Subject	= $SubjectPrefix . trim('Rooster-Alert');
	$mail->IsHTML(true);
	$mail->Body			= $HTMLMail;
				
	if(!$mail->Send()) {
		echo 'Helaas';
	} else {
		echo 'Mail verstuurd';
	}	
}
?>