<?php
// © Copyright 2011, William H. Prescott

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

