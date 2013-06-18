<?php
session_start( );
/*
Copyright © 2012 William H. Prescott. All Rights Reserved.

This file is part of Localization Editor.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY William H. Prescott "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/
/* Note javascript escape used to encode text
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
/* Preliminaries */	

	$prefix = "";
	require_once("ajax/getDatabaseList.php");
	if (isset($_COOKIE["dbName"])) {
		$dbName = $_COOKIE["dbName"];
		
		// Handle case where cookies points to a non-existence database
		if( array_search($dbName, $dbDirList) === false) {
			// change dbName to dbList[0]
			$dbName = $dbDirList[0];
			setcookie("dbName", $dbName, time()+60*60*24*30, "/");
		} 
	}
	else {
		$dbName = $dbDirList[0];
		setcookie("dbName", $dbName, time()+60*60*24*30, "/");
	}
	
	require "ajax/phpUtils.php";
	date_default_timezone_set('America/Mexico_City');
	if (isset($_COOKIE["pid"])) {
		$pid = $_COOKIE["pid"];
	}
	else {
		$pid = 1;
		setcookie("pid", $pid, time()+60*60*24*30, "/");
	}
?>
<!DOCTYPE html>

<html>
<head>
	<meta charset="UTF-8">
	<title>Localization Editor</title> 
	<link href="estilos/master.css" rel="stylesheet" type="text/css" />
	<link href="estilos/le.css" rel="stylesheet" type="text/css" />
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="http://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
	<script src="javascript/jquery.cookie.js" type="text/javascript"></script>
	<script src="javascript/jquery.scrollTo-1.4.3.1-min.js" type="text/javascript"></script>
	<script src="javascript/le.js" type="text/javascript"></script>
</head>
<body>
<?php

?>
<div id="page">
<div id="content">
<h3>
	Localization Editor
</h3>
<div class="dbSelection">
	Select database: 
	<select id="pickDB" onchange="selectDB()">
<?php
	$dbNum = 0;
	for($i=0; $i<count($dbDirList); $i++) {
		print "\t\t<option";
		print " value='".$dbDirList[$i]."'";
		if( $dbName === $dbDirList[$i]) {
			print " selected";
			$dbNum = $i;
		}
		print ">\n";
		print "\t\t\t".$dbDirList[$i]."\n";
		print "\t\t</option>\n";
	}
?>
	</select>
</div>
<div class="padded">
	<button onclick="newDB()"><big>New database …</big></button>
	<button onclick="writeLanguageFiles()"><big>Write language files</big></button>
	<button onclick="readLanguageFiles()"><big>Read language files</big></button>
</div>


<?php
	
	$queryL = "SELECT lid, langcode FROM languages ORDER BY langcode";
	$queryP = "SELECT pid, promptstring FROM prompts ORDER BY promptstring";
	$queryT = "SELECT tid,langstring FROM translations where langid=? AND promptid=?";
	$empty = array();

	// Connect to the database with PDO
	$dbPath = "Databases/".$dbDirList[$dbNum]."/localization.sqlite";
	if (!file_exists($dbPath)) {
		touch($dbPath, 0777);
		chmod($dbPath, 0777);
		$db = initDatabase ($dbPath);
		$query = "CREATE TABLE languages ".
			"(".
			"lid INTEGER PRIMARY KEY,".
			"langcode TEXT NOT NULL DEFAULT ''".
			");";

		$stmt = $db->prepare($query);
		$result = doQuery($stmt, $empty);
		
		$query = "CREATE TABLE prompts ".
			"(".
			"pid INTEGER PRIMARY KEY,".
			"promptstring TEXT NOT NULL DEFAULT ''".
			");";
		$stmt = $db->prepare($query);
		$result = doQuery($stmt, $empty);

		$query = "CREATE TABLE translations".
			"(".
			"tid INTEGER PRIMARY KEY,".
			"langid INTEGER NOT NULL DEFAULT 0,".
			"promptid INTEGER NOT NULL DEFAULT 0,".
			"langstring TEXT NOT NULL DEFAULT ''".
			");";
		$stmt = $db->prepare($query);
		$result = doQuery($stmt, $empty);
	}
	else {
		$db = initDatabase ($dbPath);
	}
	$stmtP = $db->prepare($queryP);
	$stmtP->setFetchMode(PDO::FETCH_OBJ);
	
	$result = doQuery($stmtP, $empty);
	
	$stmtT = $db->prepare($queryT);
	$stmtT->setFetchMode(PDO::FETCH_OBJ);

	print ("<div class=\"promptBox floatLeft\">\n");
	
/* Prompt selection box */	
	print ("<div class=\"promptsHdr\">\n");	
	print ("Prompt strings:<br />\n");
	print ("</div>\n");
	print ("<div class=\"prompts\" id=\"promptList\" >\n");	
	while ($record = $stmtP->fetch()) { // Returns false if no record

		print ("\n");	

		print ("<div class=\"promptEntry\">\n");	
		print ("<label>\n");
		print ("<input type=\"radio\"\n ");
		print ("\tname=\"promptSelected\"\n ");
		print ("\tid=\"prompt_".$record->{'pid'}."\"\n ");
		print ("\tvalue=\"".$record->{'pid'}."\"\n ");
		if ($record->{'pid'} == $pid) print ("\tchecked\n");
		print ("/>\n");	
		print ("\t".$record->{'promptstring'}."\n");	
		print ("</label>\n");
		print ("</div>\n");
	}
	print ("</div>\n");

/* New prompt */	
	print ("<div class=\"addPrompt\">\n");	
	print ("Add prompt:<br />\n");	
	print ("<input type=\"text\"\n");
	print ("\tid=\"addPrompt\"\n");
	print ("/><br />\n");
	print ("<input type=\"submit\"\n");
	print ("\tvalue=\"Add\"\n");
	print ("\tname=\"promptAdded\"\n");
	print ("\tonclick=\"addPrompt()\"\n");
	print ("/>\n");
	print ("</div>\n");

/* New language */	
	print ("<div class=\"addPrompt\">\n");	
	print ("Add language:<br />\n");	
	print ("<input type=\"text\"\n");
	print ("\tid=\"addLanguage\"\n");
	print ("/><br />\n");
	print ("<input type=\"submit\"\n");
	print ("\tvalue=\"Add\"\n");
	print ("\tname=\"promptAdded\"\n");
	print ("\tonclick=\"addLanguage()\"\n");
	print ("/>\n");
	print ("</div>\n");
	print ("</div>\n");

	$stmtL = $db->prepare($queryL);
	$stmtL->setFetchMode(PDO::FETCH_OBJ);
	
	$result = doQuery($stmtL, $empty);


	print ("<div class=\"languageBox floatLeft\">\n");
	
/* Language translation box */	
	while ($recordL = $stmtL->fetch()) { // Returns false if no record
		$selector = array($recordL->{'lid'}, $pid);
		$result = doQuery($stmtT, $selector);
		$recordT = $stmtT->fetch();	

		print ("\n\n<div class=\"languageEntryHdr\">\n");

//		$lid = $recordL->{'lid'};
		$tid = $recordT->{'tid'};
		print ("<input type=\"submit\"\n");
		print ("\tid=\"saveBtn-".$tid."\"\n");
		print ("\tname=\"updateTranslation\"\n");
		print ("\tvalue=\"Save\"\n");
		print ("\tdisabled\n");
		print ("\tonclick=\"updateTranslation(".$tid.")\"\n");
		print ("/>\n");
		
		print ("<input type=\"submit\"\n");
		print ("\tid=\"cancelBtn-".$tid."\"\n");
		print ("\tvalue=\"Cancel\"\n");
		print ("\tdisabled\n");
		print ("\tonclick=\"cancelTranslation()\"\n");
		print ("/>\n");
		print ($recordL->{'langcode'}.":\n");
		print ("</div>\n");
		
		print ("<div class=\"languageEntry\">\n");
		print ("<textarea \n");
		print ("\tid=\"langstring-".$tid."\"\n");
		print ("\trows=\"6\"\n");
		print ("\tcols=\"75\"\n");
		print ("\tonkeypress=\"translationTextChanged(".$tid.")\"\n");
		print (">".stripslashes($recordT->{'langstring'})."</textarea>\n");
		print ("</div>\n");
	}
	print ("</div>\n");
	print ("<div class=\"floatClear\"></div>\n");
?> 

</div><!--End of content-->
</div><!--End of page-->
</body>
</html>


