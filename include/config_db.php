<?php
# Ontwikkelomgeving
$dbHostname			= "";		// Hostname van de SQL-dB, meestal localhost
$dbUsername			= "";	// Username van de SQL-dB
$dbPassword			= "";		// Password van de SQL-dB
$dbName				= "";	// Database in de SQL-dB

define("NL", "\n");

/* Set locale to Dutch */
setlocale(LC_ALL, 'nl_NL');

$ScriptURL				= '';
$ScriptTitle			= '';	# Naam van het script (is naam van afzender in mails)
$ScriptMailAdress	= '';			# Mailadres van het script (is mailadres van afzender in mails)
$Version					= '2.0';		# Versie nummer
$SubjectPrefix		= '[3GK] ';		# Voorvoegsel bij de onderwerpregel bij het versturen van mails

$scipioParams = array(
	'Username' => '',
	'Password' => '',
	'Pincode' => ''
);

$lengthHash = 64;

?>