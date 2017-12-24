<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$db = connect_db();

if($_POST['screen'] == '1') {
	# Kerkdiensten inlezen
	$start = time();
	$eind = $start + (365*24*60*60);
	$diensten = getKerkdiensten($start, $eind);
	
	foreach($diensten as $dienst) {
		$details = getKerkdienstDetails($dienst);
		$datumString[$dienst] = date("j-n-Y", $details['start']);
	}
	
	if(in_array('p', $_POST['kolom'])) {
		# Gemeenteleden inlezen
		# Doorloop de hele groep en haal hun namen op
		$gemeenteLeden = getMembers('volwassen');
		
		foreach($gemeenteLeden as $member) {
			$namen[$member] = makeName($member, 5);
		}
	}
	
	foreach($_POST['veld'] as $r_id => $regel) {
		$output = array();
		foreach($regel as $v_id => $veld) {
			if($_POST['kolom'][$v_id] == 'd') {
				$dienstIDs = array_keys($datumString, $veld);
				
				if(count($dienstIDs) == 1) {
					$dienstID = $dienstIDs[0];
				} elseif(count($dienstIDs) > 1) {
					$dienstID = $dienstIDs[$_POST['dienst']];
				} else {
					$dienstID = '';
				}
				
				$output[$v_id] = $dienstID;
			}
			
			if($_POST['kolom'][$v_id] == 'p') {
				$persoonID = array_keys($namen, $veld);
				$output[$v_id] = $persoonID[0];
				//$output[$v_id] = $veld.' <- lid';
			}
			
			if($_POST['kolom'][$v_id] == 't') {
				$output[$v_id] = $veld;
			}
		}
		
		echo implode(';', $output) ."<br>\n";
	}
	/*	
	foreach($_POST['dienst'] as $id => $dienst) {
		//echo $dienst .' -> '. $_POST['veld'][$id][1] .'<br>';
		if($_POST['soort'] == 'insert') {
			$sql[] = "";
		} else {
			$sql[] = "UPDATE [X] SET [Y] = '". $_POST['veld'][$id][1] ."' WHERE [Z] = ". $dienst;
		}
	}
	*/
	echo implode("\n", $sql);
} elseif($_POST['screen'] == '0') {
	$rooster	= explode("\n", $_POST['rooster']);
	$regel = $rooster[1];
	$velden = explode(";", $regel);
	$max = count($velden);
	
	echo "<form method='post'>\n";
	echo "<input type='hidden' name='screen' value='1'>\n";
	echo "<input type='hidden' name='dienst' value='". $_POST['dienst'] ."'>\n";
	echo "<input type='hidden' name='soort' value='". $_POST['soort'] ."'>\n";
	echo "<table border=1>\n";
	echo "<tr>\n";		
	
	for($i=0 ; $i<$max ; $i++) {
		echo "<td>\n";
		echo "	<select name='kolom[$i]'>\n";
		echo "		<option value='d'>Dienst</option>\n";
		echo "		<option value='p'>Persoon</option>\n";
		echo "		<option value='t'>Tekst</option>\n";
		echo "	</select>\n";
		echo "	</td>\n";	
	}
	echo "</tr>\n";
			
	foreach($rooster as $r_id => $regel) {
		$velden = explode(";", $regel);
		
		echo "<tr>\n";
		foreach($velden as $v_id => $veld) {
			echo "	<td>". trim($veld) ."</td>";
			echo "	<input type='hidden' name='veld[$r_id][$v_id]' value='". trim($veld) ."'>\n";
		}		
		echo "</tr>\n";
	}
	echo "</table>\n";
	echo "<p>\n";
	echo "<input type='submit' value='Ga door'>\n";
	echo "</form>\n";	
} else {
	echo "Geef het rooster in.<br>\n";
	echo "Scheid de verschillende kolommen door <b>;</b>\n";
	echo "<p>\n";
	echo "<form method='post'>\n";
	echo "<input type='hidden' name='screen' value='0'>\n";
	echo "<table border=1>\n";
	echo "<tr>\n";
	echo "	<td rowspan='3'>\n";
	echo "	<textarea name='rooster' rows='15' cols='75'>Dump het rooster hier....</textarea><br>\n";
	echo "	</td>\n";
	echo "	<td>Scheidingsteken datum :</td>";
	echo "	<td><select name='datum'>\n";
	echo "	<option value='-'>-</option>\n";
	echo "	<option value='/'>/</option>\n";
	echo "	<option value='_'>_</option>\n";
	echo "	<option value='.'>.</option>\n";
	echo "	<option value=' '> </option>\n";
	echo "</select></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td colspan='2'><select name='dienst'>\n";
	echo "	<option value='0'>Ochtenddienst</option>\n";
	echo "	<option value='1'>Middagdienst</option>\n";
	echo "	<option value='2'>Avonddienst</option>\n";
	echo "</select></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td colspan='2'><select name='soort'>\n";
	echo "	<option value='update'>UPDATE</option>\n";
	echo "	<option value='insert'>INSERT</option>\n";
	echo "</select></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo "	<td colspan='2'>&nbsp;</td>\n";
	echo "</tr>\n";	
	echo "<tr>\n";
	echo "	<td colspan='3'><input type='submit' value='Converteer de boel'></td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
}

?>