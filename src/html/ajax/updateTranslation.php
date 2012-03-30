<?php
session_start();
// © Copyright 2012, William H. Prescott
# 
# updateTranslation.php
# W. Prescott 2012-03-29
#
include "phpUtils.php";

header('Content-type: application/json');

$uid = $_SESSION['uid'];

$tid = $_POST['tid'];
$text = $_POST['text'];

$date = time();

// Connect to the database with PDO
$db = initDatabase ('../localization.sqlite');

$query = "UPDATE translations SET langstring=? WHERE tid=?";
$stmt = $db->prepare($query);
$args = array($text, $tid);
doQuery($stmt, $args);
		


jsonReturn($result, true, 'noerror');

$db = null;
?>

