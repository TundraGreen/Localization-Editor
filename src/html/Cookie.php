<?php
// © Copyright 2011, William H. Prescott
	if (isset($_COOKIE["language"])) {
		$lang = $_COOKIE["language"];
	}
	else {
		$lang = "en_US";
	}
	if (isset($_COOKIE["pid"])) {
		$pid = $_COOKIE["pid"];
	}
	else {
		$pid = 1;
	}
?>
