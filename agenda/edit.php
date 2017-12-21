<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
$db = connect_db();

if(isset($_POST['save'])) {
	$startTijd = mktime($_POST['sUur'], $_POST['sMin'], 0, $_POST['Maand'], $_POST['Dag'], $_POST['Jaar']);
	$eindTijd = mktime($_POST['eUur'], $_POST['eMin'], 0, $_POST['Maand'], $_POST['Dag'], $_POST['Jaar']);
	
	if(isset($_POST['id'])) {
		$query = "UPDATE $TableAgenda SET $AgendaStart = '$startTijd', $AgendaEind = '$eindTijd', $AgendaTitel = '". urlencode($_POST['titel']) ."', $AgendaDescr = '". urlencode($_POST['omschrijving']) ."' WHERE $AgendaID like ". $_POST['id'];
	} else {
		$query = "INSERT INTO $TableAgenda ($AgendaStart, $AgendaEind, $AgendaTitel, $AgendaDescr) VALUES ('$startTijd', '$eindTijd', '". urlencode($_POST['titel']) ."', '". urlencode($_POST['omschrijving']) ."')";
	}
	
	mysqli_query($db, $query);
	
} elseif(isset($_REQUEST['id']) OR isset($_REQUEST['new'])) {
	$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>";
	
	if(isset($_REQUEST['id'])) {		
		$sql = "SELECT * FROM $TableAgenda WHERE $AgendaID like ". $_REQUEST['id'];
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_array($result);
		
		$Dag = date("d", $row[$AgendaStart]);
		$Maand = date("m", $row[$AgendaStart]);
		$Jaar = date("Y", $row[$AgendaStart]);
		$sUur = date("H", $row[$AgendaStart]);
		$sMin = date("i", $row[$AgendaStart]);
		$eUur = date("H", $row[$AgendaEind]);
		$eMin = date("i", $row[$AgendaEind]);
		$titel = urldecode($row[$AgendaTitel]);
		$omschrijving = urldecode($row[$AgendaDescr]);
		
		$text[] = "<input type='hidden' name='id' value='". $_REQUEST['id'] ."'>";
	} else {
		$Dag		= getParam('Dag', date("d"));
		$Maand	= getParam('Maand', date("m"));
		$Jaar		= getParam('Jaar', date("Y"));
		
		$sUur		= getParam('sUur', date("H"));
		$sMin		= getParam('sMin', date("i"));
		$eUur		= getParam('eUur', date("H", time()+3600));
		$eMin		= getParam('eMin', date("i"));
	}		
	
	$text[] = "<table>";
	$text[] = "<tr>";
	$text[] = "	<td>Datum</td>";
	$text[] = "	<td><select name='Dag'>";
	for($d=1 ; $d<32 ; $d++) {
		$text[] = "	<option value='$d'". ($d == $Dag ? ' selected' : '') .">$d</option>";
	}
	$text[] = "	</select> ";
	$text[] = "	<select name='Maand'>";
	for($m=1 ; $m<13 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $Maand ? ' selected' : '') .">". $maandArray[$m] ."</option>";
	}
	$text[] = "	</select> ";
	$text[] = "	<select name='Jaar'>";
	for($j=date("Y"); $j<=(date("Y")+10) ; $j++) {
		$text[] = "	<option value='$j'". ($j == $Jaar ? ' selected' : '') .">$j</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>Starttijd</td>";
	$text[] = "	<td><select name='sUur'>";
	for($u=0; $u<24 ; $u++) {
		$text[] = "	<option value='$u'". ($u == $sUur ? ' selected' : '') .">$u</option>";
	}
	$text[] = "	</select>";
	$text[] = "	<select name='sMin'>";
	for($m=0; $m<60 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $sMin ? ' selected' : '') .">". substr('0'.$m, -2) ."</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>Eindtijd</td>";
	$text[] = "	<td><select name='eUur'>";
	for($u=0; $u<24 ; $u++) {
		$text[] = "	<option value='$u'". ($u == $eUur ? ' selected' : '') .">$u</option>";
	}
	$text[] = "	</select>";
	$text[] = "	<select name='eMin'>";
	for($m=0; $m<60 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $eMin ? ' selected' : '') .">". substr('0'.$m, -2) ."</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>Titel</td>";
	$text[] = "	<td><input type='text' name='titel' value='$titel' size='50'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>Omschrijving</td>";
	$text[] = "	<td><textarea name='omschrijving' rows=15 cols=40>$omschrijving</textarea></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>&nbsp;</td>";
	$text[] = "	<td><input type='submit' name='save' value='Opslaan'></td>";
	$text[] = "</tr>";	
	$text[] = "</table>";
	$text[] = "</form>";
} else {
	$sql = "SELECT * FROM $TableAgenda";
	$result = mysqli_query($db, $sql);
	if($row = mysqli_fetch_array($result)) {
		do {
			$text[] = date("d-m-Y", $row[$AgendaStart]). " <a href='?id=". $row[$AgendaID] ."'>". urldecode($row[$AgendaTitel]) ."</a><br>";
		} while($row = mysqli_fetch_array($result));
	}
	
	$text[] = "<p>";
	$text[] = "<a href='?new'>nieuw</a>";
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;


?>