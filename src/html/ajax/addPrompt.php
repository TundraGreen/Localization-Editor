<?php
session_start();
// © Copyright 2012, William H. Prescott
# 
# addPrompt.php
# W. Prescott 2012-03-29
#
include "phpUtils.php";

header('Content-type: application/json');

$uid = $_SESSION['uid'];

$string = $_POST['string'];

// Connect to the database with PDO
$db = initDatabase ('../localization.sqlite');

$query = "INSERT INTO prompts (promptstring) VALUES (?)";
$stmt = $db->prepare($query);
$args = array($string);
doQuery($stmt, $args);
$pid = $db->lastinsertid();

$queryL = "SELECT lid FROM languages";
$stmtL = $db->prepare($queryL);
$stmtL->setFetchMode(PDO::FETCH_OBJ);
	
$result = doQuery($stmtL, $empty);

while ($record = $stmtL->fetch()) { // Returns false if no record
	$query = "INSERT INTO translations (langid, promptid, langstring) VALUES (?,?,?)";
	$stmt = $db->prepare($query);
	$args = array($record->{lid}, $pid, $string);
	doQuery($stmt, $args);
}

jsonReturn($result, true, 'noerror');

$db = null;
?>

