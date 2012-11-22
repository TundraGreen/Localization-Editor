<?php
/*
	Set $prefix before include
	Facilitates use in various locations
*/
$handle=opendir($prefix."Databases");

if ($handle !== false) {
	while (($file = readdir($handle))!==false) {
		if (substr($file,0,1) !== '.') 	$dbDirList[] = $file;
	}
	asort($dbDirList);
}
else {
	$dbDirList = array();
}
?>
