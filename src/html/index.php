<?php
session_start( );
// © Copyright 2011, William H. Prescott
?>

<!DOCTYPE html>
<html>
<head>
	<title>Localization Editor</title> 
	<link href="styles/le.css" rel="stylesheet" type="text/css" />
	<script src="javascript/le.js" type="text/javascript"></script>
</head>
<body onLoad="handleEvent('init')">
<?php include "Cookie.php"?>
<div id="page">
<div id="content">
<h1>
	Localization Editor
</h1>
<?php
/* Preliminaries */	

	require "ajax/phpUtils.php";
	date_default_timezone_set('America/Mexico_City');
	
	$queryL = "SELECT lid, langcode FROM languages";
	$queryP = "SELECT pid, promptstring FROM prompts ORDER BY promptstring";
	$queryT = "SELECT tid,langstring FROM translations where langid=? AND promptid=?";
	$empty = array();

	// Connect to the database with PDO
	$db = initDatabase ('Localization.sqlite');
	
	$stmtP = $db->prepare($queryP);
	$stmtP->setFetchMode(PDO::FETCH_OBJ);
	
	$result = doQuery($stmtP, $empty);
	
	$stmtT = $db->prepare($queryT);
	$stmtT->setFetchMode(PDO::FETCH_OBJ);

/* Prompt selection box */	
	print ("<div class=\"floatleft\">\n");	
	print ("<div class=\"promptsHdr\">\n");	
	print ("Prompt strings:<br />\n");
	print ("</div>\n");
	print ("<div class=\"prompts\">\n");	
	print ("<form id=\"promptList\" >\n");	
	while ($record = $stmtP->fetch()) { // Returns false if no record

		print ("\n");	

		print ("<div class=\"promptEntry\">\n");	
		print ("<input type=\"radio\"\n ");
		print ("\tname=\"promptSelected\"\n ");
		print ("\tvalue=\"".$record->{pid}."\"\n ");
		print ("\tonchange=\"handleEvent('selectPrompt')\"\n");	
		if ($record->{pid} == $pid) print ("\tchecked\n");
		print ("/>\n");	
		print ("\t".$record->{promptstring}."\n");	
		print ("</div>\n");
	}
	print ("</form>\n");
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
	print ("\tonclick=\"handleEvent('addPrompt')\"\n");
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
	print ("\tonclick=\"handleEvent('addLanguage')\"\n");
	print ("/>\n");
	print ("</div>\n");
	print ("</div>\n");

	$stmtL = $db->prepare($queryL);
	$stmtL->setFetchMode(PDO::FETCH_OBJ);
	
	$result = doQuery($stmtL, $empty);


/* Language translation box */	
	print ("<div class=\"floatleft\">\n");	
	while ($recordL = $stmtL->fetch()) { // Returns false if no record
		$selector = array($recordL->{lid}, $pid);
		$result = doQuery($stmtT, $selector);
		$recordT = $stmtT->fetch();	

		print ("<div class=\"languageEntryHdr\">\n");

		print ("<input type=\"submit\"\n");
		print ("\tname=\"updateTranslation\"\n");
		print ("\tvalue=\"Save\"\n");
		$lid = $recordL->{lid};
		$tid = $recordT->{tid};
		print ("\tonclick=\"handleEvent('updateTranslation',".$tid.")\"\n");
		print ("/>\n");
		print ("<input type=\"submit\"\n");
		print ("\tvalue=\"Cancel\"\n");
		print ("\tonclick=\"handleEvent('cancelTranslation')\"\n");
		print ("/>\n");
		print ($recordL->{langcode}.":\n");
		print ("</div>\n");
		print ("<div class=\"languageEntry\">\n");
		print ("<textarea type=\"submit\"\n");
		print ("\tid=\"langstring-".$tid."\"\n");
		print ("\trows=\"10\"\n");
		print ("\tcols=\"80\"\n");
		print (">".$recordT->{langstring}."</textarea>\n");
		print ("</div>\n");
	}
	print ("</div>\n");
?> 

</div><!--End of content-->
</div><!--End of page-->
</body>
</html>


