<?php
include_once("../../../general_include/general_config.php");
include_once("../../../general_include/shared_functions.php");
//include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();
mysqli_set_charset($db, 'utf8mb4');
$sql	= "SELECT * FROM $TableAdres WHERE ($AdresWijk like 'B' OR $AdresWijk like 'F') AND `latitude` = 0 LIMIT 0,1";

$result	= mysqli_query($db, $sql);
if($row = mysqli_fetch_array($result)) {
	echo "<html>\n";
	echo "<head>\n";
	if(!isset($id)) {
		echo "<meta http-equiv=refresh content='5;url='>\n";
	}
	echo "</head>\n";
	echo "<body>\n";
	
	do {
		$straat		= $row[$AdresStraat] .' '.$row[$AdresHuisnummer];
		$postcode	= $row[$AdresPC];
		$plaats		= $row[$AdresPlaats];
						
		$coord			= getCoordinates($straat, $postcode, $plaats);		
						
		if($coord[4] == 'ROOFTOP' OR $coord[4] == 'RANGE_INTERPOLATED' OR $coord[4] == 'APPROXIMATE' OR $coord[4] == 'GEOMETRIC_CENTER' ) {
			if($coord[4] == 'ROOFTOP') {
				$acc = 8;
			} else {
				$acc = 6;
			}
			$sql = "UPDATE $TableAdres SET `longitude` = '". $coord[0].'.'.$coord[1] ."', `latitude` = '". $coord[2].'.'.$coord[3] ."' WHERE $AdresID = ". $row[$AdresID];
		} else {			
			$sql = "UPDATE $TableAdres SET `latitude` = 10 WHERE $AdresID = ". $row[$AdresID];
		}
		
		//echo $sql;
		
		if(mysqli_query($db, $sql)) {
			echo "$row[$AdresID] ging okay<br>\n";
		} else {
			echo "$sql ging fout<br>\n";
			echo $straat;
		}		
	} while($row = mysqli_fetch_array($result));
}

?>

</body>
</html>