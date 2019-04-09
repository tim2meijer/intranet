<?php
# Ontwikkelomgeving
$dbHostname				= "";		// Hostname van de SQL-dB, meestal localhost
$dbUsername				= "";	// Username van de SQL-dB
$dbPassword				= "";		// Password van de SQL-dB
$dbName						= "";	// Database in de SQL-dB

$allowedIP				= array('', '');

$ScriptSever			= '';
$ScriptURL				= $ScriptSever.'';
$ScriptTitle			= '';	# Naam van het script (is naam van afzender in mails)
$ScriptMailAdress	= '';			# Mailadres van het script (is mailadres van afzender in mails)
$Version					= '';		# Versie nummer
$SubjectPrefix		= '';		# Voorvoegsel bij de onderwerpregel bij het versturen van mails


$scipioParams = array(
	'Username' => '',
	'Password' => '',
	'Pincode' => ''
);

# Mailchimp gegevens
$MC_apikey = '';
$MC_listid = '';
$MC_server = '';
$wijkInterest = array(
	'A' => "",
	'B' => "",
	'C' => "",
	'D' => "",
	'E' => "",
	'F' => "",
	'G' => "",
	'H' => "",
	'I' => "",
	'J' => ""
);


?>