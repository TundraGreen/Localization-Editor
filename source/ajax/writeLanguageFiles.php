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
$dbPath = "../Databases/".$dbName."/localization.sqlite";

$db = initDatabase ($dbPath);

$empty = array();

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
	$fileName =  "../Databases/".$dbName."/".$recordL->{langcode}.".php";
	$fHdl = fopen($fileName, 'w');
	$output = "<?php\n";
	fwrite($fHdl, $output);
	while ($recordP = $stmtP->fetch()) { // Returns false if no record
		$args = array($recordL->{lid}, $recordP->{pid});
		doQuery($stmtT, $args);
		$recordT = $stmtT->fetch();
		$output = "define('".$recordP->{promptstring}."', '".$recordT->{langstring}."');\n";
		fwrite($fHdl, $output);
	}
	$output = "?>\n";
	fwrite($fHdl, $output);
	fclose($fHdl);
}
jsonReturn($result, true, 'noerror');

$db = null;
?>

