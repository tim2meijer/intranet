<?php
# Ontwikkelomgeving
$dbHostname				= "";		// Hostname van de SQL-dB, meestal localhost
$dbUsername				= "";	// Username van de SQL-dB
$dbPassword				= "";		// Password van de SQL-dB
$dbName						= "";	// Database in de SQL-dB

$allowedIP				= array('', '');

$ScriptSever			= '';
$ScriptURL				= $ScriptSever;
$ScriptTitle			= '';	# Naam van het script (is naam van afzender in mails)
$ScriptMailAdress	= '';			# Mailadres van het script (is mailadres van afzender in mails)
$Version					= '';		# Versie nummer
$SubjectPrefix		= ' ';		# Voorvoegsel bij de onderwerpregel bij het versturen van mails

$scipioParams = array(
	'Username' => '',
	'Password' => '',
	'Pincode' => ''
);

# Mailchimp gegevens
$MC_apikey = '';
$MC_listid = '';
$MC_server = '';

# De verschillende kerkelijke relaties hebben allemaal een andere tag in MailChimp
$tagRelatie = array(
	'dochter' => ,
	'echtgenoot' => ,
	'echtgenote' => ,
	'gezinshoofd' => ,
	'levenspartner' => ,
	'zelfstandig' => ,
	'zoon' => 
);

# Kerkelijke status
$tagStatus = array(
	'belijdend lid' => ,
	'betrokkene' => ,
	'dooplid' => 
//	'gast' => ,
//	'gedoopt gastlid' => ,
//	'geen lid' => ,
//	'ongedoopt kind' => ,
//	'overige' => 
);

# Als het adres vanuit Scipio komt krijgt die ook een tag
$tagScipio = ;

# De verschillende maillijsten hebben allemaal een ander id in MailChimp
$ID_google = "";
$ID_wijkmails = "";
$ID_gebed_dag = "";
$ID_gebed_week = "";
$ID_gebed_maand = "";
$ID_trinitas = "";
?>