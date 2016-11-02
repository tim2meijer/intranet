<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

/*
if(!isset($_REQUEST['id'])) {
	$id = $_SESSION['ID'];
} else {
	$id = $_REQUEST['id'];
}
*/

$id = getParam('id', $_SESSION['ID']);

$personData = getMemberDetails($id);
$familie = getFamilieleden($id);
toLog('debug', $_SESSION['ID'], $id, 'profiel bekeken'); 

# Pagina tonen
echo $HTMLHeader;
echo "<h1>". makeName($id, 6) ."</h1>".NL;
echo "<table width=100% border=0>".NL;
echo "<tr>".NL;
echo "	<td width=4%>&nbsp;</td>".NL;
echo "	<td width=44% valign='top'>";

# Eigen gegevens
echo "	<table>".NL;
echo "	<tr>".NL;
echo "		<td><b>Adres</b></td>".NL;
echo "		<td><a href='https://www.google.nl/maps/place/". urlencode($personData['straat'] .' '. $personData['huisnummer'] .', '. $personData['PC'] .' '. $personData['plaats']) ."' target='_blank'>". $personData['straat'] .' '. $personData['huisnummer'] ."</a></td>".NL;
echo "	</tr>".NL;
echo "	<tr>".NL;
echo "		<td><b>Postcode</b></td>".NL;
echo "		<td>". $personData['PC'] .' '. $personData['plaats'] ."</td>".NL;
echo "	</tr>".NL;

if($personData['prive_tel'] != '' AND $personData['fam_tel'] != '') {
	echo "	<tr>".NL;
	echo "		<td><b>Telefoon</b> (familie)</td>".NL;
	echo "		<td>". $personData['fam_tel'] ."</td>".NL;
	echo "	</tr>".NL;
	echo "	<tr>".NL;
	echo "		<td><b>Telefoon</b> (prive)</td>".NL;
	echo "		<td>". $personData['prive_tel'] ."</td>".NL;
	echo "	</tr>".NL;
} else {
	echo "		<td><b>Telefoon</b></td>".NL;
	echo "		<td>". $personData['tel'] ."</td>".NL;
	echo "	</tr>".NL;
}

if($personData['prive_mail'] != '' AND $personData['fam_mail'] != '') {
	echo "	<tr>".NL;
	echo "		<td><b>Mailadres</b> (familie)</td>".NL;
	echo "		<td><a href='mailto:". makeName($id, 5) ." <".$personData['fam_mail'] .">'>". $personData['fam_mail'] ."</td>".NL;
	echo "	</tr>".NL;
	echo "	<tr>".NL;
	echo "		<td><b>Mailadres</b> (prive)</td>".NL;
	echo "		<td><a href='mailto:". makeName($id, 5) ." <".$personData['prive_mail'] .">'>". $personData['prive_mail'] ."</td>".NL;
	echo "	</tr>".NL;
} else {
	echo "		<td><b>Mailadres</b></td>".NL;
	echo "		<td><a href='mailto:". makeName($id, 5) ." <".$personData['mail'] .">'>". $personData['mail'] ."</td>".NL;
	echo "	</tr>".NL;
}

echo "	<tr>".NL;
echo "		<td><b>Wijk</b></td>".NL;
echo "		<td>".$personData['wijk'] ."</td>".NL;
echo "	</tr>".NL;
echo "	<tr>".NL;
echo "		<td><b>Geboortedatum</b></td>".NL;
echo "		<td>". strftime("%d %B %Y", $personData['geb_unix']) ."</td>".NL;
echo "	</tr>".NL;
if($personData['twitter'] != '' OR $personData['fb'] != '' OR $personData['linkedin'] != '') {
	echo "	<tr>".NL;
	echo "		<td>&nbsp;</td>".NL;
	echo "		<td>";
	if($personData['fb'] != "")				{	echo "		<a href='https://www.facebook.com/".$personData['fb']."' target='_blank'><img src='images/facebook.jpg'></a>&nbsp;"; }
	if($personData['linkedin'] != "")	{	echo "		<a href='https://nl.linkedin.com/in/".$personData['linkedin']."' target='_blank'><img src='images/linkedin.jpg'></a>&nbsp;"; }
	if($personData['twitter'] != "")	{	echo "		<a href='https://twitter.com/".$personData['twitter']."' target='_blank'><img src='images/twitter.png'></a>"; }
	echo "		</td>".NL;
	echo "	</tr>".NL;
}
echo "	</table>".NL;
echo "	</td>".NL;
echo "	<td width=4%>&nbsp;</td>".NL;

# Familieleden
echo "	<td width=44% valign='top'>";

if(count($familie) > 1) {
	echo "	<b>Familieleden</b><br>";
	foreach($familie as $leden) {
		if($leden != $id) {
			$famData = getMemberDetails($leden);
			echo "	<a href='?id=$leden'>". makeName($leden, 5) ."</a> ('". substr($famData['jaar'], -2) .")<br>";
		}
	}
} else {
	echo '	&nbsp;';
}

echo "	</td>".NL;
echo "	<td width=4%>&nbsp;</td>".NL;
echo "</tr>".NL;

if(in_array($_SESSION['ID'], $familie) OR in_array(1, getMyGroups($_SESSION['ID']))) {
	echo "<tr>".NL;
	echo "	<td>&nbsp;</td>".NL;
	echo "	<td valign='top'><a href='gegevens.php?id=$id'>Wijzig gegevens</a></td>".NL;
	
	if(in_array(1, getMyGroups($_SESSION['ID']))) {			
		echo "	<td>&nbsp;</td>".NL;
		echo "	<td valign='top'>".NL;
		echo "	<a href='admin/editGegevens.php?action=splits&id=$id'>Splits familielid af</a><br>".NL;
		echo "	<a href='admin/editGegevens.php?action=combine&id=$id'>Combineer persoon</a><br>".NL;
		echo "	<a href='admin/editGegevens.php?action=add'>Voeg persoon toe</a><br>".NL;
		echo "	<a href='admin/editGegevens.php?action=addFam&id=$id'>Voeg familielid toe</a><br>".NL;
		echo "	<a href='admin/editGegevens.php?action=remove&id=$id'>Verwijder familielid</a><br>".NL;
		echo "	<a href='admin/editGegevens.php?action=removeFam&id=$id'>Verwijder hele familie</a><br>".NL;
		echo "	</td>".NL;
		echo "	<td valign='top'>&nbsp;</td>".NL;
	} else {
		echo "	<td colspan='3'>&nbsp;</td>".NL;
	}
	echo "</tr>".NL;
}

echo "</table>".NL;

echo $HTMLFooter;

?>
