<?php
include_once('../include/functions.php');
include_once('../include/config.php');

$db = connect_db();

$sql_plan = "ALTER TABLE $TablePlanning ADD `". $PlanningUser ."_new` INT NOT NULL AFTER `$PlanningUser`";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TableGrpUsr ADD `". $GrpUsrUser ."_new` INT NOT NULL AFTER `$GrpUsrUser`";
mysqli_query($db, $sql_plan);

//$sql_plan = "ALTER TABLE $TableOldUsers ADD `kopie` INT NOT NULL AFTER `$oldUserID`";
//mysqli_query($db, $sql_plan);

$sql_convert = "SELECT * FROM $TableOldUsers";
$result_convert = mysqli_query($db, $sql_convert);
if($row_convert = mysqli_fetch_array($result_convert)) {
	do {
		$oldID		= $row_convert[$oldUserID];
		$newID		= $row_convert[$oldUserScipioID];		
		$username = $row_convert[$oldUserUsername];
		$password	= $row_convert[$oldUserPassword];
		//$hash			= $row_convert[$oldUserHash];
		$hash = generateID($lengthHash);
		
		$sql_1 = "UPDATE $TableUsers SET $UserUsername = '$username', $UserPassword = '$password', $UserHash = '$hash' WHERE $UserID like '$newID'";
		mysqli_query($db, $sql_1);
		
		$sql_2 = "UPDATE $TablePlanning SET ". $PlanningUser ."_new = '$newID' WHERE $PlanningUser like '$oldID'";
		mysqli_query($db, $sql_2);
		
		$sql_3 = "UPDATE $TableGrpUsr SET ". $GrpUsrUser ."_new = '$newID' WHERE $GrpUsrUser like '$oldID'";
		mysqli_query($db, $sql_3);

		//$sql_4 = "UPDATE $TableOldUsers SET `kopie` = '1' WHERE $oldUserID like '$oldID'";
		//mysqli_query($db, $sql_4);
		
	} while($row_convert = mysqli_fetch_array($result_convert));
}

$sql_mislukt = "SELECT $TableOldUsers.$oldUserVoornaam, $TableOldUsers.$oldUserAchternaam FROM $TablePlanning,$TableOldUsers WHERE $TablePlanning.". $PlanningUser ."_new = 0 AND $TablePlanning.$PlanningUser = $TableOldUsers.$oldUserID GROUP BY $TablePlanning.". $PlanningUser;
$result_mislukt = mysqli_query($db, $sql_mislukt);
if($row_mislukt = mysqli_fetch_array($result_mislukt)) {
	echo "<h1>$TablePlanning</h1>\n";
	do {
		echo $row_mislukt[$oldUserVoornaam] ." ". $row_mislukt[$oldUserAchternaam] ." niet gevonden<br>\n";
	} while($row_mislukt = mysqli_fetch_array($result_mislukt));
}

$sql_mislukt = "SELECT $TableOldUsers.$oldUserVoornaam, $TableOldUsers.$oldUserAchternaam FROM $TableGrpUsr,$TableOldUsers WHERE $TableGrpUsr.". $GrpUsrUser ."_new = 0 AND $TableGrpUsr.$GrpUsrUser = $TableOldUsers.$oldUserID GROUP BY $TableGrpUsr.". $GrpUsrUser;
$result_mislukt = mysqli_query($db, $sql_mislukt);
if($row_mislukt = mysqli_fetch_array($result_mislukt)) {
	echo "<h1>$TableGrpUsr</h1>\n";
	do {
		echo $row_mislukt[$oldUserVoornaam] ." ". $row_mislukt[$oldUserAchternaam] ." niet gevonden<br>\n";
	} while($row_mislukt = mysqli_fetch_array($result_mislukt));
}

$sql_plan = "ALTER TABLE $TablePlanning CHANGE `$PlanningUser` `". $PlanningUser ."_old` INT(6) NOT NULL";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TablePlanning CHANGE `". $PlanningUser ."_new` `$PlanningUser` INT(6) NOT NULL";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TableGrpUsr CHANGE `$GrpUsrUser` `". $GrpUsrUser ."_old` INT(6) NOT NULL";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TableGrpUsr CHANGE `". $GrpUsrUser ."_new` `$GrpUsrUser` INT(6) NOT NULL";
mysqli_query($db, $sql_plan);


/*
$sql_plan = "ALTER TABLE $TablePlanning DROP $PlanningUser";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TablePlanning CHANGE `". $PlanningUser ."_new` `$PlanningUser` INT(6) NOT NULL";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TableGrpUsr DROP $GrpUsrUser";
mysqli_query($db, $sql_plan);

$sql_plan = "ALTER TABLE $TableGrpUsr CHANGE `". $GrpUsrUser ."_new` `$GrpUsrUser` INT(6) NOT NULL";
mysqli_query($db, $sql_plan);
*/

//"ALTER TABLE `group_member` DROP `lid`";
//"ALTER TABLE `group_member` CHANGE `lid_new` `lid` INT(11) NOT NULL;";
?>