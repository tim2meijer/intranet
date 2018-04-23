<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$geslacht			= getParam('geslacht', '');
$sDag					= getParam('sDag', 1);
$sMaand				= getParam('sMaand', 1);
$sJaar				= getParam('sJaar', 1900);
$eDag					= getParam('eDag', date("d"));
$eMaand				= getParam('eMaand', date("m"));
$eJaar				= getParam('eJaar', date("Y"));
$wijk					= getParam('wijk', '');
$searchString	= getParam('searchString', '');

$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>";
$text[] = "<table>";
$text[] = "<tr>";
$text[] = "	<td align='center'>Geslacht</td>";
$text[] = "	<td align='center'>&nbsp;</td>";
$text[] = "	<td align='center'>Geboren na</td>";
$text[] = "	<td align='center'>&nbsp;</td>";
$text[] = "	<td align='center'>Geboren voor</td>";
$text[] = "	<td align='center'>&nbsp;</td>";
$text[] = "	<td align='center'>Wijk</td>";
$text[] = "	<td align='center'>&nbsp;</td>";
$text[] = "	<td align='center'>(deel van) naam</td>";
$text[] = "</tr>";
$text[] = "<tr>";
$text[] = "	<td>";
$text[] = "	<select name='geslacht'>";
$text[] = "	<option value=''". ($geslacht == '' ? ' selected' : '') .">Man of vrouw</option>";
$text[] = "	<option value='M'". ($geslacht == 'M' ? ' selected' : '') .">Man</option>";
$text[] = "	<option value='V'". ($geslacht == 'V' ? ' selected' : '') .">Vrouw</option>";
$text[] = "	</select>";
$text[] = "	</td>";
$text[] = "	<td>&nbsp;</td>";
$text[] = "	<td><select name='sDag'>";
for($d=1 ; $d<32 ; $d++) {
	$text[] = "	<option value='$d'". ($d == $sDag ? ' selected' : '') .">$d</option>";
}
$text[] = "	</select> - ";
$text[] = "	<select name='sMaand'>";
for($m=1 ; $m<13 ; $m++) {
	$text[] = "	<option value='$m'". ($m == $sMaand ? ' selected' : '') .">". $maandArray[$m] ."</option>";
}
$text[] = "	</select> - ";
$text[] = "	<select name='sJaar'>";
for($j=1900 ; $j<=date("Y") ; $j++) {
	$text[] = "	<option value='$j'". ($j == $sJaar ? ' selected' : '') .">$j</option>";
}
$text[] = "	</select>";
$text[] = "	</td>";
$text[] = "	<td>&nbsp;</td>";
$text[] = "	<td><select name='eDag'>";
for($d=1 ; $d<32 ; $d++) {
	$text[] = "	<option value='$d'". ($d == $eDag ? ' selected' : '') .">$d</option>";
}
$text[] = "	</select> - ";
$text[] = "	<select name='eMaand'>";
for($m=1 ; $m<13 ; $m++) {
	$text[] = "	<option value='$m'". ($m == $eMaand ? ' selected' : '') .">". $maandArray[$m] ."</option>";
}
$text[] = "	</select> - ";
$text[] = "	<select name='eJaar'>";
for($j=1900 ; $j<=date("Y") ; $j++) {
	$text[] = "	<option value='$j'". ($j == $eJaar ? ' selected' : '') .">$j</option>";
}
$text[] = "	</select>";
$text[] = "	</td>";
$text[] = "	<td>&nbsp;</td>";
$text[] = "	<td valign='top'><select name='wijk'>";
$text[] = "	<option value=''". ($wijk == '' ? ' selected' : '') .">Alle wijken</option>";
foreach($wijkArray as $w) {
	$text[] = "	<option value='$w'". ($w == $wijk ? ' selected' : '') .">Wijk $w</option>";
}
$text[] = "	</select></td>";
$text[] = "	<td>&nbsp;</td>";
$text[] = "	<td><input type='text' name='searchString' value='$searchString'></td>";
//$text[] = "	<td>&nbsp;</td>";
$text[] = "</tr>";
$text[] = "<tr>";
$text[] = "	<td colspan='9'>&nbsp;</td>";
$text[] = "</tr>";
$text[] = "<tr>";
$text[] = "	<td colspan='9' align='center'><input type='submit' name='search' value='Zoeken'></td>";
$text[] = "</tr>";
$text[] = "</table>"; 

if(isset($_POST['search'])) {
	toLog('debug', $_SESSION['ID'], '', "Gezocht op S:$searchString G:$geslacht W:$wijk B:$sDag-$sMaand-$sJaar E:$eDag-$eMaand-$eJaar");
	
	$where[] = "$UserGeboorte BETWEEN '$sJaar-$sMaand-$sDag' AND '$eJaar-$eMaand-$eDag'";
	$where[] = "$UserStatus like 'actief'";
	$table[] = $TableUsers;
	
	if($geslacht != '') {
		$where[] = "$UserGeslacht like '$geslacht'";
	}
	
	if($searchString != '') {
		$searchString = strtolower($searchString);
		$having1 = ", LOWER(CONCAT_WS(' ', $UserVoornaam, $UserAchternaam)) as naamKort, LOWER(CONCAT_WS(' ', $UserVoornaam, $UserTussenvoegsel, $UserAchternaam)) as naamLang";
		$having2 = " HAVING naamKort like '%$searchString%' OR naamKort like '$searchString%' OR naamKort like '%$searchString'	OR naamLang like '%$searchString%' OR naamLang like '$searchString%' OR naamLang like '%$searchString'";
	} else {
		$having1 = '';
		$having2 = '';
	}
		
	if($wijk != '') {
		$where[] = "$UserWijk like '$wijk'";
	}
	
	$sql = "SELECT $TableUsers.$UserID$having1 FROM ". implode(', ', $table)." WHERE ". implode(' AND ', $where) ."$having2 ORDER BY $UserAchternaam";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		$text[] = '<p>';
		$text[] = '<ol>';
		do {
			$lid = $row[$UserID];
			$text[] = "<li><a href='profiel.php?id=$lid'>". makeName($lid, 5) ."</a></li>";
			$ids[] = $lid;			
			
			$ouders = getParents($lid);			
			foreach($ouders as $ouder) {
				$parentIDs[$ouder] = $ouder;
			}
			
		} while($row = mysqli_fetch_array($result));		
		$text[] = '</ol>';
		
		$text[] = "<a href='admin/exportGroupMembers.php?ids=".implode('|', $ids)."'>Exporteer deze gegevens</a>";
		$text[] = "<a href='admin/exportGroupMembers.php?ids=".implode('|', $parentIDs)."'>Exporteer de ouders van deze gegevens</a>";
		
	}
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;
?>