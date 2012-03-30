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
?>