<?php
include_once('include/functions.php');
include_once('include/config.php');
include_once('include/HTML_TopBottom.php');

$db = connect_db();

$kind = 399;
$ouders = getParents($kind);

echo $ouders[0] .'|'. $ouders[1];

?>