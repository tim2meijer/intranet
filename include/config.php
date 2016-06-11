<?php

$dbHostname			= "";		// Hostname van de SQL-dB, meestal localhost
$dbUsername			= "";	// Username van de SQL-dB
$dbPassword			= "";		// Password van de SQL-dB
$dbName				= "";	// Database in de SQL-dB

define("NL", "\n");

/* Set locale to Dutch */
setlocale(LC_ALL, 'nl_NL');

//$ScriptURL				= '';			# Map waar het script staat, bv http://www.example.com/scripts/funda/
$ScriptTitle			= '';	# Naam van het script (is naam van afzender in mails)
$ScriptMailAdress	= '';			# Mailadres van het script (is mailadres van afzender in mails)
$Version					= '1.0';		# Versie nummer
$SubjectPrefix		= '[3GK] ';		# Voorvoegsel bij de onderwerpregel bij het versturen van mails

# Tabel- en veldnamen voor de verschillende tabellen in MySQL
$TableUsers					= "leden";
$UserID							= "id";
$UserAdres					= "kerk_adres";
$UserGeslacht				= "geslacht";
$UserVoorletters		= "voorletters";
$UserVoornaam				= "voornaam";
$UserTussenvoegsel	= "tussenvoegsel";
$UserAchternaam			= "achternaam";
$UserMeisjesnaam		= "meisjesnaam";
$UserUsername				= "username";
$UserPassword				= "password";
$UserGebDag					= "geboortedag";
$UserGebMaand				= "geboortemaand";
$UserGebJaar				= "geboortejaar";
$UserTelefoon				= "telefoon";
$UserMail						= "email";
$UserTwitter				= "twitter";
$UserFacebook				= "facebook";
$UserLinkedin				= "linkedin";
$UserBelijdenis			= "belijdenis";

$TableAdres					= "adressen";
$AdresID						= "id";
$AdresStraat				= "straat";
$AdresHuisnummer		= "nummer";
$AdresPC						= "postcode";
$AdresPlaats				= "plaats";
$AdresMail					= "mail";
$AdresTelefoon			= "telefoon";
$AdresWijk					= "wijk";

$TableGroups				= "groepen";
$GroupID						= "id";
$GroupNaam					= "naam";
$GroupHTMLIn				= "html_intern";
#$GroupShowIn				= "show_intern";
$GroupHTMLEx				= "html_extern";
#$GroupShowEx				= "show_extern";
$GroupBeheer				= "beheerder";

$TableRoosters			= "roosters";
$RoostersID					= "id";
$RoostersNaam				= "naam";
$RoostersGroep			= "groep";
$RoostersMail				= "mail";
$RoostersSubject		= "onderwerp";

$TableGrpUsr				= "group_member";
$GrpUsrGroup				= "commissie";
$GrpUsrUser					= "lid";

$TableDiensten			= "kerkdiensten";
$DienstID						= "id";
$DienstStart				= "start";
$DienstEind					= "eind";

$TablePlanning			= "planning";
$PlanningDienst			= "dienst";
$PlanningGroup			= "commissie";
$PlanningUser				= "lid";

?>