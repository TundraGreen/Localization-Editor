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
# addDatabase.php
# W. Prescott 2012-11-21
#

header('Content-type: application/json');
require_once "phpUtils.php";
$prefix = "../";
require_once("getDatabaseList.php");

$dbName = $_POST['name'];
foreach($dbDirList as $db ) {
	if($dbName === $db) {
		jsonReturn("Name taken",false, 'name collision');
		exit();
	}
}
mkdir("../Databases/".$dbName);
$dbPath = "../Databases/".$dbName."/localization.sqlite";
$fHdl = fopen($dbPath,'a');
fclose($fHdl);

$db = initDatabase ($dbPath);
// Connect to the database with PDO

$query[] = <<<ENDQUERY
CREATE TABLE languages
(
lid INTEGER PRIMARY KEY,
langcode TEXT NOT NULL DEFAULT ''
);
ENDQUERY;

$query[] = <<<ENDQUERY
CREATE TABLE prompts
(
pid INTEGER PRIMARY KEY,
promptstring TEXT NOT NULL DEFAULT ''
);
ENDQUERY;

$query[] = <<<ENDQUERY
CREATE TABLE translations
(
tid INTEGER PRIMARY KEY,
langid INTEGER NOT NULL DEFAULT 0,
promptid INTEGER NOT NULL DEFAULT 0,
langstring TEXT NOT NULL DEFAULT ''
);
ENDQUERY;

foreach($query as $q) {
	$stmt = $db->prepare($q);
	$args = array();
	$result = doQuery($stmt, $args);
}
$db = null;
jsonReturn($result, true, 'noerror');
?>

