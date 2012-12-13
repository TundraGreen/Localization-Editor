/*jslint devel: true, undef: true, unparam: true, vars: true, white: true, maxerr: 50, indent: 4, plusplus: false, browser: true */
/*
Copyright © 2012 William H. Prescott. All Rights Reserved.

This file is part of Localization Editor.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY William H. Prescott "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/


"use strict";

// someTextAreaChanged keeps track of changed translation blocks
// it is used to prevent user from changing prompts with unsaved changed blocks
var someTextAreaChanged = [];


$(document).ready(function() {
	$('#promptList').change(function() {
		if (someTextAreaChanged.length > 0) {
			if (confirm("Some text has changed.\n"
				+ "Switching prompts will lose the changes.\n"
				+ "Click OK to discard changes.\n"
				+ "Click Cancel to go back without losing changes.")) {
			}
			else {
				$('[name="promptSelected" ][value="' + $.cookie('pid') + '"]').attr('checked', true)
				return;         
			}
		}
		$.cookie('pid', $("input[name='promptSelected']:checked").val(), {path:"/"});
		window.location.reload(true);							
	});
});
    

function addLanguage () {
	var langCode = $("#addLanguage").val();
	$.ajax({
		type: "POST",
		url: "ajax/addLanguage.php",
		data: {string: langCode},
		complete: function(data) {
			var jsonObj = JSON.parse(data.responseText);
			if (!jsonObj.resultFlag) {
				console.log("Response: "+jsonObj.error);
			}
			window.location.reload(true);							
		}
	});
}

function addPrompt () {
	var prompt = $("#addPrompt").val();
	$.ajax({
		type: "POST",
		url: "ajax/addPrompt.php",
		data: {string: prompt},
		complete: function(data) {
			var jsonObj = JSON.parse(data.responseText);
			if (!jsonObj.resultFlag) {
				console.log("Response: "+jsonObj.error);
			}
			window.location.reload(true);							
		}
	});
}

function cancelTranslation () {
	window.location.reload(true);
}

function newDB () {
	var name = prompt('Enter name for new database');
	if (name === null || name === '') {
		return;
	}
	$.ajax({
		type: "POST",
		url: "ajax/addDatabase.php",
		data: {name: name},
		complete: function(data) {
			var jsonObj = JSON.parse(data.responseText);
			if (!jsonObj.resultFlag) {
				console.log("Response: "+jsonObj.error);
			}
			window.location.reload(true);							
		}
	});
}

function pushUnique(arr, val) {
    for(var i=0; i<arr.length; i++) {
        if(arr[i] == val) {
            return;
        }
    }
	arr.push(val);
}

function removeByValue(arr, val) {
    for(var i=0; i<arr.length; i++) {
        if(arr[i] == val) {
            arr.splice(i, 1);
            break;
        }
    }
}
function selectDB () {
	$.cookie('dbName',$('#pickDB option:selected').val(),{path:"/"});
	window.location.reload(true);
}

function translationTextChanged (tid) {
	$("#saveBtn-"+tid).removeAttr('disabled');							
	$("#cancelBtn-"+tid).removeAttr('disabled');
	pushUnique(someTextAreaChanged, tid);							
}

function updateTranslation (tid) {
	var langStr = $("#langstring-"+tid).val();
	$.ajax({
		type: "POST",
		url: "ajax/updateTranslation.php",
		data: {tid: tid, text: langStr},
		complete: function(data) {
			var jsonObj = JSON.parse(data.responseText);
			if (!jsonObj.resultFlag) {
				console.log("Response: "+jsonObj.error);
			}
			$("#saveBtn-"+tid).attr("disabled", "disabled");							
			$("#cancelBtn-"+tid).attr("disabled", "disabled");
			removeByValue(someTextAreaChanged, tid);
		}
	});
}

function writeFiles() {
	$.ajax({
		url:"ajax/writeLanguageFiles.php",
		complete: function(data) {
			var jsonObj = JSON.parse(data.responseText);
			if (jsonObj.resultFlag) {
				alert("Files written");
			}
			else {
				console.log("Error: "+jsonObj.error);
			}
		}
	});	
}


/* ------------------------ Debug utilities --------------------------------*/
function dumpProperties(obj, objName) {
	var tp = typeof(obj);
	var i;
	for (i in obj) {
		if (true) {
			try {
				console.log(objName + "." + i + " = " + obj[i]);
			}
			catch (err) {
				console.log("Error for: " + objName + "." + i);
				console.log("Error description: " + err.description);
			}
		}
	}
	return;
}
function countProperties(obj, objName) {
	var tp = typeof(obj);
	console.log("Type of " + objName + ": " + tp);
	var count = 0;
	var i;
	for (i in obj) {
		if (true) {
			count++;
		}
	}
	console.log("Count: " + count);
}



