<?php
/*
Copyright © 2012 William H. Prescott. All Rights Reserved.

This file is part of Localization Editor.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY William H. Prescott "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

function initDatabase($name) {
	try {
		$db = new PDO("sqlite:$name");
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
	} 
	catch (PDOException $e) {
		$errorMessage = "dbName: ".$name."\nerrMessage: ".$e->getMessage()."\n";
		jsonReturn('initDatabase', false, $errorMessage);
		exit();
	}
	return $db;
}

function doQuery($stmt, $args) {
	try {
		$stmt->execute($args);
	}
	catch (PDOException $e) {
		$errorMessage = "errMessage: ".$e->getMessage()."\n";
		jsonReturn('doQuery', false, $errorMessage);
		exit();
	}
}

function fixDisplayText ($string) {
	$outString = $string;
	$outString = stripslashes($outString);
	$outString = nl2br($outString, true);
	
	return $outString;
}

function jsonReturn($output, $resultFlag, $errorMessage) {
	/*
		output: results of ajax request
		resultCode: true if ok, false if error
		errorMessage: wrongpassword | nosuchuser | collision | etc
	*/
	$return = array(
		'output' => $output, 
		'resultFlag' => $resultFlag, 
		'errorMessage' => $errorMessage
	);
	print json_encode($return);	
}

// Given a prompt string
// Returns the corresponding string in selected language 
// Language choice is taken from the language cookie
function local($text) {
	global $lid;
	$db = initDatabase('../localization.sqlite');
	
	// Get id for prompt
	$queryP = "SELECT pid FROM prompts where promptstring=?";
	$stmtP = $db->prepare($queryP);
	$stmtP->setFetchMode(PDO::FETCH_OBJ);
	doQuery($stmtP, array($text)); 
	$recordP = $stmtP->fetch();
	$pid =  $recordP->{pid};
	
	// Get local string for lid and pid
	$queryT = "SELECT langstring FROM translations where langid=? AND promptid=?;";
	$stmtT = $db->prepare($queryT);
	$stmtT->setFetchMode(PDO::FETCH_OBJ);
	doQuery($stmtT, array($lid, $pid)); 
	$recordT = $stmtT->fetch();
	$result = $recordT->{langstring};
	return $result;
}

?>

