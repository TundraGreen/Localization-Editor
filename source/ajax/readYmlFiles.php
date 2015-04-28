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
require_once "Spyc.php";


if (isset($_COOKIE["dbName"])) {
	$dbName = $_COOKIE["dbName"];
}
elseif ($argv[1] == 'beacon2') {
  $dbName = $argv[1];
}
else {
	jsonReturn($dbName, true, 'No DB name');
}


// Connect to the database with PDO

date_default_timezone_set('America/Mexico_City');

// Connect to the database with PDO
$dbDir = "../Databases/".$dbName;
$dbPath = $dbDir."/".$dbName.".sqlite";
print $dbPath."\n";

$empty = array();
$db = initDatabase ($dbPath);

$fileListTmp = scandir($dbDir);
$return = '';
foreach ($fileListTmp as $elem) {
	if ($elem !== $dbName.".sqlite" &&
		$elem[0] !== '.') $fileList[] = $elem;
}


$translations = [];
foreach ($fileList as $fileName) {
  $language = basename($fileName, '.yml');
  print "language: $language\n";

	// Insert language into database
// 	$langName = substr($fileName, 0, strpos($fileName, '.php'));
// 	$query = 'INSERT INTO languages (langcode) VALUES (?)';
// 	$stmt = $db->prepare($query);
// 	$result = doQuery($stmt, array($langName));

	// Retrieve complete list of translations as a single string
	$yaml = Spyc::YAMLLoad($dbDir."/".$fileName);
  $contents = $yaml[$language];

  foreach ($contents as $key=>$value) {
    $prompt = $key;
    if (is_array($value)) {
      processArray($prompt, $value);
    }
    else {
//       print("$language: $prompt: $value\n");
    	$translations[$prompt][$language] = $value;
    }
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
 		$result = doQuery($stmt, array($lid, $pid, stripslashes($translation)));
	}
}

jsonReturn($return, true, 'noerror');

$db = null;

function processArray ($prompt, $input) {
  global $translations, $language;
  foreach ($input as $key=>$value) {
    $newPrompt = $prompt.'.'.$key;
    if (is_array($value)) {
      processArray($newPrompt, $value);
    }
    else {
//       print("$language: $newPrompt: $value\n");
    	$translations[$newPrompt][$language] = $value;
    }
  }
}
?>
