<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();
$filename		= 'SelectieDraijer.csv';
$scipio_raw	= file($filename);

$kop = explode(";", array_shift($scipio_raw));

$GeboorteID = array_search('Geboortedatum', $kop);
$AchternaamID = array_search('Achternaam', $kop);
$RoepnaamID = array_search('Roepnaam', $kop);
$RegnrID = array_search('Regnr.', $kop);

for($i=0; $i<8 ; $i++) {
	set_time_limit(30);
	echo "<h1>$i</h1>\n";
	foreach($scipio_raw as $rij) {
		$velden = explode(";", $rij);
		$id			= $velden[$RegnrID];
		
		$sql = "SELECT * FROM $TableUsers WHERE $oldUserScipioID = $id";
		$result = mysqli_query($db, $sql);
		
		if(mysqli_num_rows($result) == 0) {
			$datum	= replaceDatum($velden[$GeboorteID]);
			$naam		= $velden[$AchternaamID];
			$roepnaam		= $velden[$RoepnaamID];
			
			if($i==0)			$sql = "SELECT * FROM $TableUsers WHERE $oldUserGeboorte like '$datum' AND $oldUserScipioID = 0";
			elseif($i==1)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserAchternaam like '$naam' AND $oldUserGeboorte like '$datum' AND $oldUserScipioID = 0";
			elseif($i==2)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserVoornaam like '$roepnaam' AND $oldUserAchternaam like '$naam' AND $oldUserGeboorte like '$datum' AND $oldUserScipioID = 0";
			elseif($i==3)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserMeisjesnaam like '$naam' AND $oldUserGeboorte like '$datum' AND $oldUserScipioID = 0";
			elseif($i==4)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserVoornaam like '$roepnaam' AND $oldUserMeisjesnaam like '$naam' AND $oldUserGeboorte like '$datum' AND $oldUserScipioID = 0";
			elseif($i==5)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserAchternaam like '$naam' AND $oldUserScipioID = 0";
			elseif($i==6)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserMeisjesnaam like '$naam' AND $oldUserScipioID = 0";
			elseif($i==7)	$sql = "SELECT * FROM $TableUsers WHERE $oldUserVoornaam like '$roepnaam' AND $oldUserScipioID = 0";
			$result = mysqli_query($db, $sql);
			
			if(mysqli_num_rows($result) == 1) {
				$row = mysqli_fetch_array($result);
				$sql_update = "UPDATE $TableUsers SET $oldUserScipioID = $id WHERE $oldUserID = $row[$oldUserID]";
				$result = mysqli_query($db, $sql_update);
			} elseif(mysqli_num_rows($result) > 1) {
				echo "meerdere hits gevonden : ". $id ."|$sql<br>";
			}
		}
	}
}

function replaceDatum($datum) {
	 $datum = str_replace (' jan ', '-01-' , $datum);
	 $datum = str_replace (' feb ', '-02-' , $datum);
	 $datum = str_replace (' mrt ', '-03-' , $datum);
	 $datum = str_replace (' apr ', '-04-' , $datum);
	 $datum = str_replace (' mei ', '-05-' , $datum);
	 $datum = str_replace (' jun ', '-06-' , $datum);
	 $datum = str_replace (' jul ', '-07-' , $datum);
	 $datum = str_replace (' aug ', '-08-' , $datum);
	 $datum = str_replace (' sep ', '-09-' , $datum);
	 $datum = str_replace (' okt ', '-10-' , $datum);
	 $datum = str_replace (' nov ', '-11-' , $datum);
	 $datum = str_replace (' dec ', '-12-' , $datum);
	 
	 $delen = explode('-', $datum);
	 
	 return $delen[2].'-'.$delen[1].'-'.substr('0'.$delen[0], -2);
}

?>