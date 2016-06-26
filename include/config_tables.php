<?php

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
$RoostersFrom				= "naam_afzender";
$RoostersFromAddr		= "mail_afzender";

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
$PlanningPositie		= "positie";

$TableLog						= "log";
$LogID							= "id";
$LogTime						= "tijd";
$LogType						= "type";
$LogUser						= "dader";
$LogSubject					= "slachtoffer";
$LogMessage					= "message";

$wijkArray = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
$maandArray = array(1 => 'jan', 2 => 'feb', 3 => 'mrt', 4 => 'apr', 5 => 'mei', 6 => 'jun', 7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'dec');


?>