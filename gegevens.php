<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

$id				= getParam('id', $_SESSION['ID']);
$familie	= getFamilieleden($_SESSION['ID']);

# Als je niet voorkomt in de Admin-groep en je bent geen familie
# dan ga je naar je eigen gegevens
if(!in_array(1, getMyGroups($_SESSION['ID'])) AND !in_array($id,$familie)) {	
	$id = $_SESSION['ID'];
}

if(isset($_POST['data_opslaan'])) {
	$sql_persoon = "UPDATE $TableUsers SET `$UserVoorletters` = '". addslashes($_POST['voorletters']) ."', `$UserVoornaam` = '". addslashes($_POST['voornaam']) ."', `$UserTussenvoegsel` = '". addslashes($_POST['tussenvoegsel']) ."', `$UserAchternaam` = '". addslashes($_POST['achternaam']) ."', `$UserMeisjesnaam` = '". addslashes($_POST['meisjesnaam']) ."', `$UserGeboorte` = '". $_POST['jaar'].'-'.$_POST['maand'].'-'.$_POST['dag'] ."', `$UserTelefoon` = '". $_POST['prive_tel'] ."', `$UserMail` = '". $_POST['prive_mail'] ."', `$UserTwitter` = '". $_POST['twitter'] ."', `$UserFacebook` = '". $_POST['fb'] ."', `$UserLinkedin` = '". $_POST['linkedin'] ."'	WHERE `$UserID` = ". $_POST['id'];
	$sql_adres = "UPDATE $TableAdres SET `$AdresStraat` = '". addslashes($_POST['straat']) ."', `$AdresHuisnummer` = '". $_POST['huisnummer'] ."', `$AdresPC` = '". $_POST['pc'] ."', `$AdresPlaats` = '". addslashes($_POST['plaats']) ."', `$AdresTelefoon` = '". $_POST['fam_tel'] ."', `$AdresMail` = '". $_POST['fam_mail'] ."', `$AdresWijk` = '". $_POST['wijk'] ."' WHERE $AdresID = ". $_POST['adresID'];
	
	if(!mysqli_query($db, $sql_persoon) OR !mysqli_query($db, $sql_adres)) {
		$text[] = "Er is een fout opgetreden.";
		$text[] = $sql_persoon;
		$text[] = $sql_adres;
		toLog('error', $_SESSION['ID'], $_POST['id'], 'Fout bij wijzigen gegevens');
	} else {
		$text[] = "Gegevens succesvol gewijzigd.";
		toLog('info', $_SESSION['ID'], $_POST['id'], 'Gegevens gewijzigd');
	}
	
	$redirect = $ScriptURL."/profiel.php?id=".$_POST['id'];
	$url="Location: ". $redirect;
	//header($url);			
	header("Refresh: 1; url=".$redirect);
} else {
	$personData = getMemberDetails($id);	
	
	$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>";
	$text[] = "<input type='hidden' name='id' value='$id'>";
	$text[] = "<input type='hidden' name='adresID' value='". $personData['adres'] ."'>";	
	$text[] = "<table border=0 width=100%>";
	$text[] = "<tr>";
	$text[] = "<td width=4%>&nbsp;</td>";
	$text[] = "<td width=44%><h1>Eigen gegevens</h1></td>";
	$text[] = "<td width=4%>&nbsp;</td>";
	$text[] = "<td width=44%><h1>Familie gegevens</h1></td>";
	$text[] = "<td width=4%>&nbsp;</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "<td width=4%>&nbsp;</td>";
	$text[] = "<td width=44% valign='top'>";
	$text[] = "<table width='100%'>";
	if(in_array(1, getMyGroups($_SESSION['ID']))) {
		$text[] = "<tr>";
		$text[] = "	<td>Geslacht</td>";
		$text[] = "	<td><select name='geslacht'>";
		$text[] = "	<option value='M'". ($personData['geslacht'] == 'M' ? ' selected' : '') .">Man</option>";
		$text[] = "	<option value='V'". ($personData['geslacht'] == 'V' ? ' selected' : '') .">Vrouw</option>";
		$text[] = "	</select>";
		$text[] = "	</td>";
		$text[] = "</tr>";
		$text[] = "<tr>";
		$text[] = "	<td>Status</td>";
		$text[] = "	<td><select name='belijdenis'>";
		$text[] = "	<option value='-'". ($personData['belijdenis'] == '-' ? ' selected' : '') ."></option>";
		$text[] = "	<option value='D'". ($personData['belijdenis'] == 'D' ? ' selected' : '') .">Gedoopt</option>";
		$text[] = "	<option value='B'". ($personData['belijdenis'] == 'B' ? ' selected' : '') .">Belijdenis</option>";
		$text[] = "	</select>";
		$text[] = "	</td>";
		$text[] = "</tr>";
	}
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Voorletters</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='voorletters' value='".$personData['voorletters']."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Voornaam</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='voornaam' value='".$personData['voornaam']."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Tussenvoegsel</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='tussenvoegsel' value='".$personData['tussenvoegsel']."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Achternaam</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='achternaam' value='".$personData['achternaam']."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Meisjesnaam</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='meisjesnaam' value='".$personData['meisjesnaam']."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Geboortedag</td>";
	$text[] = "	<td valign='top' colspan='2'><select name='dag'>";
	for($d=1 ; $d<32 ; $d++) {
		$text[] = "	<option value='$d'". ($d == $personData['dag'] ? ' selected' : '') .">$d</option>";
	}
	$text[] = "	</select> - ";
	$text[] = "	<select name='maand'>";
	for($m=1 ; $m<13 ; $m++) {
		$text[] = "	<option value='$m'". ($m == $personData['maand'] ? ' selected' : '') .">". $maandArray[$m] ."</option>";
	}
	$text[] = "	</select> - ";
	$text[] = "	<select name='jaar'>";
	for($j=1900 ; $j<=date("Y") ; $j++) {
		$text[] = "	<option value='$j'". ($j == $personData['jaar'] ? ' selected' : '') .">$j</option>";
	}
	$text[] = "	</select>";
	$text[] = "</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Telefoonnummer</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='prive_tel' value='". $personData['prive_tel'] ."'><br><font class='small'>Vul dit nummer alleen in als het verschilt van de rest van de familie</font></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Mailadres</td>";
	$text[] = "	<td valign='top' colspan='2'><input type='text' name='prive_mail' value='". $personData['prive_mail'] ."'><br><font class='small'>Vul dit mailadres alleen in als het verschilt van de rest van de familie</font></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Twitter</td>";
	$text[] = "	<td valign='top' class='small' align='right'>https://twitter.com/</td>";
	$text[] = "	<td valign='top'><input type='text' name='twitter' value='". $personData['twitter'] ."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Facebook</td>";
	$text[] = "	<td valign='top' class='small' align='right'>https://www.facebook.com/</td>";
	$text[] = "	<td valign='top'><input type='text' name='fb' value='". $personData['fb'] ."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>LinkedIn</td>";
	$text[] = "	<td valign='top' class='small' align='right'>https://nl.linkedin.com/in/</td>";
	$text[] = "	<td valign='top'><input type='text' name='linkedin' value='". $personData['linkedin'] ."'></td>";
	$text[] = "</tr>";
	$text[] = "</table>";
	$text[] = "</td>";
	$text[] = "<td width=4% valign='top'>";
	$text[] = "<td width=44% valign='top'>";
	$text[] = "<table width='100%'>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Adres</td>";
	$text[] = "	<td valign='top'><input type='text' name='straat' value='".$personData['straat']."'> <input type='text' name='huisnummer' value='".$personData['huisnummer']."' size='3'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Postcode + plaats</td>";
	$text[] = "	<td valign='top'><input type='text' name='pc' value='".$personData['PC']."' size='6'> <input type='text' name='plaats' value='".$personData['plaats']."'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Telefoonnummer</td>";
	$text[] = "	<td valign='top'><input type='text' name='fam_tel' value='". $personData['fam_tel'] ."'><br><font class='small'>Vul hier het telefoonnummer in dat u deelt met de rest van de familie</font></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Mailadres</td>";
	$text[] = "	<td valign='top'><input type='text' name='fam_mail' value='". $personData['fam_mail'] ."'><br><font class='small'>Vul hier het mailadres in dat u deelt met de rest van de familie</font></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td valign='top'>Wijk</td>";
	$text[] = "	<td valign='top'><select name='wijk'>";
	foreach($wijkArray as $wijk) {
		$text[] = "	<option value='$wijk'". ($wijk == $personData['wijk'] ? ' selected' : '') .">Wijk $wijk</option>";
	}
	$text[] = "	</select></td>";
	$text[] = "</tr>";
	$text[] = "</table>";
	$text[] = "</td>";
	$text[] = "<td width=4%>&nbsp;</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td colspan='5' align='center'><input type='submit' name='data_opslaan' value='Opslaan'></td>";
	$text[] = "</tr>";
	$text[] = "</table>";
}

echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;

?>