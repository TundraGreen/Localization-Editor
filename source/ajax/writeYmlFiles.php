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

//
// writeLanguageFiles.php
// W. Prescott 2012-11-18
//
/* Note javascript encodeURI used to encode text
	php urldecode used to read text
	http://www.the-art-of-web.com/javascript/escape/
	When storing:
	They are encodeURI by javascript before sending to php
	Then php urldecodes them before sending to sqlite
	sqlite automatically adds backslashes to single quotes
	When displaying in editor:
	php stripslashes strips slashes from them.
	When writing language files:
	php leaves the backslashed single quotes backslashed
*/

header('Content-type: application/json');

include_once "phpUtils.php";


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
$dbPath = "../Databases/".$dbName."/".$dbName.".sqlite";
// print $dbPath;

$db = initDatabase ($dbPath);

$empty = [];

$queryL = "SELECT lid, langcode FROM languages";
$queryP = "SELECT pid, promptstring FROM prompts ORDER BY promptstring";
$queryT = "SELECT tid,langstring FROM translations where langid=? AND promptid=?";

$stmtL = $db->prepare($queryL);
$stmtL->setFetchMode(PDO::FETCH_OBJ);

$stmtP = $db->prepare($queryP);
$stmtP->setFetchMode(PDO::FETCH_OBJ);

$stmtT = $db->prepare($queryT);
$stmtT->setFetchMode(PDO::FETCH_OBJ);

$result = doQuery($stmtL, $empty);

while ($recordL = $stmtL->fetch()) { // Returns false if no record
	$result .= "\n".doQuery($stmtP, $empty);
	$fileName =  "../Databases/".$dbName."/".$recordL->{'langcode'}.".yml";
	$fHdl = fopen($fileName, 'w');
  $translations = [];
	while ($recordP = $stmtP->fetch()) { // Returns false if no record
		$args = array($recordL->{'lid'}, $recordP->{'pid'});
		doQuery($stmtT, $args);
		$recordT = $stmtT->fetch();
		$translations[$recordP->{'promptstring'}] = $recordT->{'langstring'};
	}
	$yaml = processTranslations($recordL->{'langcode'}, $translations);
  fwrite($fHdl, $yaml);
	$output = "\n";
	fwrite($fHdl, $output);
	fclose($fHdl);
}


jsonReturn($result, true, 'noerror');

$db = null;

function processTranslations($language, $translations) {

  $yaml = $language. ":\n";
  $promptAtLevel = [];
  for ($i=0; $i< 20; $i++) {
    $promptAtLevel[$i] = '';
  }

  $debug = 0;
  foreach ($translations as $key=>$value) {
//     print ("language: $language  -  debug: $debug\n");
//     print("prompt: $key\n");
    $promptArray = explode('.', $key);
    $currentIndentLevel = 0;
    $currentIndentString = '  ';

    $differ = false;
    // This was added to handle cases like:
    //         active_record.errors.models.hospital.attributes.data_center:
    //         active_record.errors.models.incidents.attributes.data_center:

    for ($i=0; $i< count($promptArray); $i++) {
//       print("i: $i\n");
//       print("$i: $promptAtLevel[$i] : $promptArray[$i]\n");
      if (($promptAtLevel[$i] != $promptArray[$i]) || $differ) {
        $differ = true;
        $yaml .= $currentIndentString;
        $yaml .= $promptArray[$i];
        $promptAtLevel[$i] = $promptArray[$i];
        if ($i != count($promptArray) - 1) {
          $yaml .= ":\n";
        }
        else {
          $yaml .= ": |\n";
          $yaml .= $currentIndentString;
          $patchedValue = str_replace("\n", "\n  ".$currentIndentString, $value);
          $yaml .= "  $patchedValue\n";
        }
      }
      $currentIndentLevel += 1;
      $currentIndentString = '  ' . $currentIndentString;
    }
//     print $yaml;
//     if ($debug > 2) break;
//     $debug++;
  }
  return $yaml;
}


?>

