<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
include_once('../../../general_include/class.phpmailer.php');
include_once('../../../general_include/class.html2text.php');

$db = connect_db();

if(isset($_POST['opvragen'])) {
	$invoer	= $_POST['invoer'];
	//$sql		= "SELECT * FROM $TableUsers WHERE $UserUsername like '$invoer' OR $UserMail like '$invoer'";
	$sql		= "SELECT $TableUsers.$UserID FROM $TableUsers, $TableAdres WHERE $TableUsers.$UserUsername like '$invoer' OR $TableUsers.$UserMail like '$invoer' OR ($TableUsers.$UserAdres = $TableAdres.$UserID AND $TableAdres.$AdresMail like '$invoer')";
	$result = mysqli_query($db, $sql);
			
	if(mysqli_num_rows($result) == 0) {
		$text[] = "Er is helaas niks gevonden met '$invoer'";
	} else {
		$row	= mysqli_fetch_array($result);
		$id		= $row[$UserID];
		$data = getMemberDetails($id);
		
		$nieuwPassword = generatePassword(12);
		
		$sql_update = "UPDATE $TableUsers SET $UserPassword = '". md5($nieuwPassword) ."' WHERE $UserID = $id";
		mysqli_query($db, $sql_update);
								
		$Mail[] = "Beste ". $data['voornaam'] .",";
		$Mail[] = "<p>";
		$Mail[] = "je hebt een nieuw wachtwoord aangevraagd voor $ScriptTitle.<br>";
		$Mail[] = "Je kan inloggen met :";
		$Mail[] = "<p>";
		$Mail[] = "Loginnaam : ". $data['username'] ."<br>";
		$Mail[] = "Wachtwoord : ". $nieuwPassword;
		$Mail[] = "<p>";
		//$Mail[] = "Met deze gegevens kan je via <a href='". $ScriptURL ."account.php'>". $ScriptURL ."account.php</a> je eigen wachtwoord instellen";	
		$HTMLMail = implode("\n", $Mail);
		
		echo $HTMLMail;

		if(!sendMail($id, "Nieuw wachtwoord voor $ScriptTitle", $HTMLMail, $var)) {			
			$text[] = "Inloggegevens konden helaas niet verstuurd worden";
			//toLog('error', $userID, '', 'problemen met account-mail versturen');
		} else {
			//toLog('info', $id, '', "Inloggegevens verstuurd naar ". makeName(array($data['voornaam'], $data['tussen'], $data['achternaam']), 5));
			$text[] = "Inloggegevens zijn verstuurd";
		}
	}	
} else {
	$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>\n";
	$text[] = "<table>";
	$text[] = "<tr>";
	$text[] = "	<td>Voer uw loginnaam of email-adres in. Het systeem zal dan een nieuw wachtwoord mailen.</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td><input type='text' name='invoer' value='". $_REQUEST['invoer'] ."' size='75'></td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td>&nbsp;</td>";
	$text[] = "</tr>";
	$text[] = "<tr>";
	$text[] = "	<td align='center'><input type='submit' name='opvragen' value='Opvragen'></td>";
	$text[] = "</tr>";
	$text[] = "</table>";
	$text[] = "</form>";
}

# verdeelBlokken(implode("\n", $text));
echo $HTMLHeader;
echo implode("\n", $text);
echo $HTMLFooter;
?>