<?php
define("NL", "\n");

/* Set locale to Dutch */
setlocale(LC_ALL, 'nl_NL');

$ScriptURL				= '';
$ScriptTitle			= '';	# Naam van het script (is naam van afzender in mails)
$ScriptMailAdress	= '';			# Mailadres van het script (is mailadres van afzender in mails)
$Version					= '2.1';		# Versie nummer
$SubjectPrefix		= '[3GK] ';		# Voorvoegsel bij de onderwerpregel bij het versturen van mails

$lengthShortHash = 16;
$lengthLongHash = 64;

# Tabel- en veldnamen voor de verschillende tabellen in MySQL
$TableUsers					= "leden";
$UserID							= "scipio_id";
$UserStatus					= "status";
$UserAdres					= "kerk_adres";
$UserGeslacht				= "geslacht";
$UserVoorletters		= "voorletters";
$UserVoornaam				= "voornaam";
$UserTussenvoegsel	= "tussenvoegsel";
$UserAchternaam			= "achternaam";
$UserMeisjesnaam		= "meisjesnaam";
$UserStraat					= "straat";
$UserHuisnummer			= "nummer";
$UserToevoeging			= "toevoeging";
$UserPC							= "postcode";
$UserPlaats					= "plaats";
$UserGeboorte				= "geboortedatum";
$UserTelefoon				= "telefoon";
$UserMail						= "email";
$UserBelijdenis			= "belijdenis";
$UserBurgelijk			= "burgstaat";
$UserRelatie				= "relatie";
$UserLastChange			= "last_change";
$UserLastVisit			= "last_visit";
$UserWijk						= "wijk";
$UserUsername				= "username";
$UserPassword				= "password";
$UserHash						= "hash";
$UserHashShort			= "hash_short";
$UserHashLong				= "hash_long";

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
$RoostersFields			= "aantal";
$RoostersMail				= "mail";
$RoostersSubject		= "onderwerp";
$RoostersFrom				= "naam_afzender";
$RoostersFromAddr		= "mail_afzender";
$RoostersGelijk			= "gelijke_diensten";
$RoostersLastChange	= "last_change";

$TableGrpUsr				= "group_member";
$GrpUsrGroup				= "commissie";
$GrpUsrUser					= "lid";

$TableDiensten			= "kerkdiensten";
$DienstID						= "id";
$DienstStart				= "start";
$DienstEind					= "eind";
$DienstVoorganger		= "voorganger";
$DienstCollecte_1		= "collecte_1";
$DienstCollecte_2		= "collecte_2";
$DienstOpmerking		= "opmerking";

$TablePlanning			= "planning";
$PlanningDienst			= "dienst";
$PlanningGroup			= "commissie";
$PlanningUser				= "lid";
$PlanningPositie		= "positie";

$TableAgenda				= "agenda";
$AgendaID 					= "id";
$AgendaStart 				= "start";
$AgendaEind 				= "eind";
$AgendaTitel				= "titel";
$AgendaDescr 				= "beschrijving";
$AgendaComment			= "eigenaar";

$TableLog						= "log";
$LogID							= "id";
$LogTime						= "tijd";
$LogType						= "type";
$LogUser						= "dader";
$LogSubject					= "slachtoffer";
$LogMessage					= "message";

$wijkArray			= array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
$statusArray		= array('actief', 'afgemeld', 'afgevoerd', 'onttrokken', 'overleden', 'vertrokken');
$burgelijkArray	= array('gehuwd', 'gereg. partner', 'gescheiden', 'ongehuwd', 'weduwe', 'weduwnaar');
$gezinArray			= array('dochter', 'echtgenoot', 'echtgenote', 'gezinshoofd', 'levenspartner', 'zelfstandig', 'zoon');
$kerkelijkArray	= array('belijdend lid', 'betrokkene', 'dooplid', 'gast', 'gedoopt gastlid', 'geen lid', 'ongedoopt kind', 'overige');
$maandArray			= array(1 => 'jan', 2 => 'feb', 3 => 'mrt', 4 => 'apr', 5 => 'mei', 6 => 'jun', 7 => 'jul', 8 => 'aug', 9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'dec');
$letterArray		= array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

?>