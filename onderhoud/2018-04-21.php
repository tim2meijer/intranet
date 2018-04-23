<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

#Tabel hernoemen
$sql_1 = "RENAME TABLE $TableUsers TO $TableOldUsers";
mysqli_query($db, $sql_1);

$sql_5 = "CREATE TABLE `$TableUsers` (";
$sql_5 .= "  `$UserID` int(6) NOT NULL,";
$sql_5 .= "  `$UserStatus` text NOT NULL,";
$sql_5 .= "  `$UserAdres` int(8) NOT NULL,";
$sql_5 .= "  `$UserGeslacht` set('M','V') NOT NULL,";
$sql_5 .= "  `$UserVoorletters` text NOT NULL,";
$sql_5 .= "  `$UserVoornaam` text NOT NULL,";
$sql_5 .= "  `$UserTussenvoegsel` text NOT NULL,";
$sql_5 .= "  `$UserAchternaam` text NOT NULL,";
$sql_5 .= "  `$UserMeisjesnaam` text NOT NULL,";
$sql_5 .= "  `$UserStraat` text NOT NULL,";
$sql_5 .= "  `$UserHuisnummer` text NOT NULL,";
$sql_5 .= "  `$UserToevoeging` text NOT NULL,";
$sql_5 .= "  `$UserPC` text NOT NULL,";
$sql_5 .= "  `$UserPlaats` text NOT NULL,";
$sql_5 .= "  `$UserGeboorte` date NOT NULL,";
$sql_5 .= "  `$UserTelefoon` text NOT NULL,";
$sql_5 .= "  `$UserMail` text NOT NULL,";
$sql_5 .= "  `$UserBelijdenis` text NOT NULL,";
$sql_5 .= "  `$UserBurgelijk` text NOT NULL,";
$sql_5 .= "  `$UserRelatie` text NOT NULL,";
$sql_5 .= "  `$UserLastChange` int(11) NOT NULL,";
$sql_5 .= "  `$UserLastVisit` int(11) NOT NULL,";
$sql_5 .= "  `$UserWijk` text NOT NULL,";
$sql_5 .= "  `$UserUsername` text NOT NULL,";
$sql_5 .= "  `$UserPassword` text NOT NULL,";
$sql_5 .= "  `$UserHash` text NOT NULL";
$sql_5 .= ") ENGINE=InnoDB DEFAULT CHARSET=latin1;";
mysqli_query($db, $sql_5);

?>