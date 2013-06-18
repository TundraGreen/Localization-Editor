<?php
session_start();
/*
Copyright © 2012 William H. Prescott. All Rights Reserved.

This file is part of Localization Editor.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY William H. Prescott "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/
# 
# addLanguage.php
# W. Prescott 2012-03-29
#

header('Content-type: application/json');
require_once "phpUtils.php";
//$prefix = "../";
//require_once("getDatabaseList.php");

if (isset($_COOKIE["dbName"])) {
	$dbName = $_COOKIE["dbName"];
}
else {
	jsonReturn($dbName, true, 'No DB name');
}


$uid = $_SESSION['uid'];

$langString = $_POST['string'];

// Connect to the database with PDO
$dbPath = "../Databases/".$dbName."/".$dbName.".sqlite";

$db = initDatabase ($dbPath);

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
	$promptString = $record->{'promptstring'};
	$query = "INSERT INTO translations (langid, promptid, langstring) VALUES (?,?,?)";
	$stmt = $db->prepare($query);
	$args = array($lid, $record->{'pid'}, $promptString);
	doQuery($stmt, $args);
}

jsonReturn($result, true, 'noerror');

$db = null;
/*
*/
?>

