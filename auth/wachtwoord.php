<?php
include_once('../include/functions.php');
include_once('../include/config.php');
include_once('../include/HTML_TopBottom.php');
include_once('../../../general_include/class.phpmailer.php');
include_once('../../../general_include/class.html2text.php');

$db = connect_db();

if(isset($_POST['opvragen'])) {
	$invoer	= $_POST['invoer'];
	$sql		= "SELECT $UserID FROM $TableUsers WHERE $UserUsername like '$invoer' OR $UserMail like '$invoer'";
	//$sql		= "SELECT $TableUsers.$UserID FROM $TableUsers WHERE $TableUsers.$UserUsername like '$invoer' OR $TableUsers.$UserMail like '$invoer'";
	$result = mysqli_query($db, $sql);
			
	if(mysqli_num_rows($result) == 0) {
		$text[] = "Er is helaas niks gevonden met '$invoer'";
	} else {
		$row	= mysqli_fetch_array($result);
		$id		= $row[$UserID];
		$data = getMemberDetails($id);
		
		$Mail[] = "Beste ". $data['voornaam'] .",";
		$Mail[] = "<p>";
		$Mail[] = "je hebt een nieuw wachtwoord aangevraagd voor $ScriptTitle.<br>";
		$Mail[] = "<p>";
		$Mail[] = "Door <a href='". $ScriptURL ."/account.php?hash=". $data['hash'] ."'>deze link</a> te volgen kun je een wachtwoord instellen.<br>";
		$Mail[] = "Iemand met deze link kan zonder in te loggen bij je account, wees er dus zuinig op.";
		
		$HTMLMail = implode("\n", $Mail);
		
		echo $$HTMLMail;

		if(!sendMail($id, "Nieuw wachtwoord voor $ScriptTitle", $HTMLMail, $var)) {			
			toLog('error', $id, '', 'problemen met wachtwoord-mail versturen');
			$text[] = "Inloggegevens konden helaas niet verstuurd worden";			
		} else {
			toLog('info', $id, '', "Inloggegevens verstuurd naar ". makeName($id, 5));
			$text[] = "Inloggegevens zijn verstuurd";
		}
	}	
} else {
	$text[] = "<form action='". $_SERVER['PHP_SELF'] ."' method='post'>\n";
	$text[] = "<table>";
	$text[] = "<tr>";
	$text[] = "	<td>Voer uw loginnaam of email-adres in. Het systeem zal dan een link sturen waarmee u een nieuw wachtwoord kunt instellen.</td>";
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