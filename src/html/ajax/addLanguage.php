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

$langString = $_POST['string'];

// Connect to the database with PDO
$db = initDatabase ('../localization.sqlite');

$query = "INSERT INTO languages (langcode) VALUES (?)";
$stmt = $db->prepare($query);
$args = array($langString);
doQuery($stmt, $args);
$lid = $db->lastinsertid();

$queryP = "SELECT pid, promptstring FROM prompts";
$stmtP = $db->prepare($queryP);
$stmtP->setFetchMode(PDO::FETCH_OBJ);
	
$result = doQuery($stmtP, $empty);

while ($record = $stmtP->fetch()) { // Returns false if no record
	$promptString = $record->{promptstring};
	$query = "INSERT INTO translations (langid, promptid, langstring) VALUES (?,?,?)";
	$stmt = $db->prepare($query);
	$args = array($lid, $record->{pid}, $promptString);
	doQuery($stmt, $args);
}

jsonReturn($result, true, 'noerror');

$db = null;
?>

