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
/*

v 1.0 
# 
# readLanguageFiles.php
#© W. Prescott 2013-06-17
#
*/

header('Content-type: application/json');

require_once "phpUtils.php";

if (isset($_COOKIE["dbName"])) {
	$dbName = $_COOKIE["dbName"];
}
else {
	jsonReturn($dbName, true, 'No DB name');
}


// Connect to the database with PDO
	
date_default_timezone_set('America/Mexico_City');

// Connect to the database with PDO
$dbDir = "../Databases/".$dbName;
$dbPath = $dbDir."/".$dbName.".sqlite";
$empty = array();
$db = initDatabase ($dbPath);

$fileListTmp = scandir($dbDir);
$return = '';
foreach ($fileListTmp as $elem) {
	if ($elem !== $dbName.".sqlite" &&
		$elem !== '.' &&
		$elem !== '..'
	) $fileList[] = $elem;
}
foreach ($fileList as $fileName) {

	// Insert language into database
	$langName = substr($fileName, 0, strpos($fileName, '.php'));
	$query = 'INSERT INTO languages (langcode) VALUES (?)';
	$stmt = $db->prepare($query);
	$result = doQuery($stmt, array($langName));
	
	// Retrieve complete list of translations as a single string
	$string = file_get_contents($dbDir."/".$fileName);

	// Get rid of opening < ? php
	$string = str_replace(chr(60).chr(63)."php", "", $string);

	// Get rid of closing ? >
	$string = str_replace(chr(63).chr(62), "", $string);

	// Get rid of leading and trailing white space
	$string = trim($string);
	
	// Get rid of ); at end of each line
	$string = str_replace(");", "", $string);

	// Get rid of leading define(
	$string = ltrim($string,"define(");

	// Split on define(
	$definitions = explode("define(", $string);
/*
	// This obsolete code uses split on commas to parse the prompts
	foreach ($definitions as $line) {
	
		// Split on commas
		$lineElems = explode (",", $line);

		// Get rid of leading and trailing white space
		$prompt = trim($lineElems[0]);
		$langString = trim($lineElems[1]);
		
		// Get rid of single quotes
		$prompt = trim($prompt, "'");
		$langString = trim($langString, "'");
		$translations[$prompt][$langName] = $langString;
	}
*/
	// Use occurrence of double quotes to parse the prompts
	foreach ($definitions as $entry) {
		
		// Find double quotes
		$p1 = strpos($entry, 34, 0);
		$p2 = strpos($entry, 34, $p1+1);
		$p3 = strpos($entry, 34, $p2+1);
		$p4 = strpos($entry, 34, $p3+1);
		
		// Extract prompt and translation
		$prompt = substr($entry, $p1+1, $p2-$p1-1);
		$langString = substr($entry, $p3+1, $p4-$p3-1);
		
		// Store in doubly indexed array
		$translations[$prompt][$langName] = $langString;
	}
}	

foreach ($translations as $prompt => $langTranPairs) {

	// Insert prompt into database
	$query = 'INSERT INTO prompts (promptstring) VALUES (?)';
	$stmt = $db->prepare($query);
	$result = doQuery($stmt, array($prompt));
	
	// Insert translations into database
	foreach ($langTranPairs as $lang => $translation) {
	
		// Get id of language
		$query = 'SELECT lid FROM languages WHERE langcode = ?';
		$stmt = $db->prepare($query);
		$stmt->setFetchMode(PDO::FETCH_OBJ);
		$result = doQuery($stmt, array($lang));
		$record = $stmt->fetch();
		$lid = $record->{'lid'};
		
		// Get id of prompt
		$query = 'SELECT pid FROM prompts where promptstring = ?';
		$stmt = $db->prepare($query);
		$stmt->setFetchMode(PDO::FETCH_OBJ);
		$result = doQuery($stmt, array($prompt));
		$record = $stmt->fetch();
		$pid = $record->{'pid'};
		
		// Insert translation
		$query = 'INSERT INTO translations (langid, promptid, langstring) VALUES (?, ?, ?)';
		$stmt = $db->prepare($query);
		$result = doQuery($stmt, array($lid, $pid, $translation));
	}
}
/*
*/

jsonReturn($return, true, 'noerror');

$db = null;
?>

