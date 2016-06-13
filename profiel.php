<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');
$cfgProgDir = 'auth/';
include($cfgProgDir. "secure.php");
$db = connect_db();

if(!isset($_REQUEST['id'])) {
	$id = $_SESSION['ID'];
} else {
	$id = $_REQUEST['id'];
}

$personData = getMemberDetails($id);
$familie = getFamilieleden($id);

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
echo "		<td>". $personData['straat'] .' '. $personData['huisnummer'] ." (<a href='https://www.google.nl/maps/place/". urlencode($personData['straat'] .' '. $personData['huisnummer'] .', '. $personData['PC'] .' '. $personData['plaats']) ."' target='_blank'>google maps</a>)</td>".NL;
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
echo "		<td>". substr('0'.$personData['dag'], -2) .'-'. substr('0'.$personData['maand'], -2) .'-'. $personData['jaar'] ."</td>".NL;
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
			echo "	<a href='?id=$leden'>". makeName($leden, 5) ."</a> (". $famData['jaar'] .")<br>";
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
	echo "	<td colspan='3'><a href='gegevens.php?id=$id'>Wijzig gegevens</a></td>".NL;
	echo "	<td>&nbsp;</td>".NL;
	echo "</tr>".NL;
}

echo "</table>".NL;

echo $HTMLFooter;

?>
