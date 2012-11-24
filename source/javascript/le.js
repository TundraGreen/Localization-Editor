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

var localization;


/**
* This class provides general methods for all page objects.
* Most buttons and checkboxes on the page extend this objec
    @class Prototype for all web page control elements. 
*/
function ControlElement(id) {
	this.element = document.getElementById(id);
	this.disable = function () {
		this.element.disabled = true;
	};
	this.enable = function () {
		this.element.disabled = false;
	};
	this.getEnabled = function () {
		return !this.element.disabled;
	};
	this.getState = function () {
		return this.element.checked;
	};
	this.getValue = function () {
		return this.element.value;
	};
	this.setState = function () {
		this.element.checked = true;
	};
	this.setValue = function (val) {
		this.element.value = val;
	};
}

/**
	@class Handles selecting a prompt string
*/
function SelectPrompt(id, ut) {
	ControlElement.call(this, id);
	this.onChange = function (local) {
		var plElem = document.getElementById('promptList');
		var radioBtnElem = document.forms['promptList'].elements;
		var chkdValue = getCheckedValue(radioBtnElem);
		if (local.someTextAreaChanged) {
			if (confirm("Some text has changed.\n"
				+ "Switching prompts will lose the changes.\n"
				+ "Click OK to discard changes.\n"
				+ "Click Cancel to go back without losing changes.")) {
			}
			else {
				return;		
			}
		}
		$.cookie('pid', chkdValue, {path:"/"});
		window.location.reload(true);
	};
	// From http://www2.somacon.com/p516.php
	// return the value of the radio button that is checked
	// return an empty string if none are checked, or
	// there are no radio buttons
	function getCheckedValue(radioObj) {
		if(!radioObj)
			return "";
		var radioLength = radioObj.length;
		if(radioLength == undefined)
			if(radioObj.checked)
				return radioObj.value;
			else
				return "";
		for(var i = 0; i < radioLength; i++) {
			if(radioObj[i].checked) {
				return radioObj[i].value;
			}
		}
		return "";
	}
	
	// From http://www2.somacon.com/p516.php
	// set the radio button with the given value as being checked
	// do nothing if there are no radio buttons
	// if the given value does not exist, all the radio buttons
	// are reset to unchecked
	function setCheckedValue(radioObj, newValue) {
		if(!radioObj)
			return;
		var radioLength = radioObj.length;
		if(radioLength == undefined) {
			radioObj.checked = (radioObj.value == newValue.toString());
			return;
		}
		for(var i = 0; i < radioLength; i++) {
			radioObj[i].checked = false;
			if(radioObj[i].value == newValue.toString()) {
				radioObj[i].checked = true;
			}
		}
	}
}
SelectPrompt.prototype = new ControlElement();

/**
	@class Handles updating a translation block
*/
function UpdateTranslation(id, ut) {
	ControlElement.call(this, id);
	// Note:
	// in editRecord, id is the id of the record being edited
	// in addRecord, id is the id of related record in another table
	//	ie in adding a comment, id is the id of the owning answer record
	//	ie in adding a answer, id is the id of the owning question record
	//
	this.onClick = function (tid) {
		var saveBtnElem = document.getElementById('saveBtn-' + tid);
		var cancelBtnElem = document.getElementById('cancelBtn-' + tid);
		var editorTextareaElem = document.getElementById('langstring-' + tid);
		var text = editorTextareaElem.value;
		var url = "ajax/updateTranslation.php";
		var request = "tid=" + tid;
		request += "&text=" + encodeURIComponent(text);
		
		var xmlHttp = ut.ajaxFunction();
		xmlHttp.onreadystatechange = function () {
			if(xmlHttp.readyState === 4) {
				var jsonObj = JSON.parse(xmlHttp.responseText);
				if (!jsonObj.resultFlag) {
					console.log("Response: "+xmlHttp.responseText);
				}						
				window.location.reload();
				saveBtnElem.disabled = true;
				cancelBtnElem.disabled = true;
			}
		};
		xmlHttp.open("POST",url,true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", request.length);
		xmlHttp.setRequestHeader("Connection", "close");		
		xmlHttp.send(request);
	};
	this.onKeyPress = function(tid) {
		var saveBtnElem = document.getElementById('saveBtn-' + tid);
		var cancelBtnElem = document.getElementById('cancelBtn-' + tid);
		saveBtnElem.disabled = false;
		cancelBtnElem.disabled = false;
	};
}
UpdateTranslation.prototype = new ControlElement();
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

function selectDB () {
	$.cookie('dbName',$('#pickDB option:selected').val(),{path:"/"});
	window.location.reload(true);
}

function writeFiles() {
	$.ajax({
		url:"ajax/writeLanguageFiles.php",
		complete: function(data) {
			var jsonObj = JSON.parse(data.responseText);
			if (!jsonObj.resultFlag) {
				console.log("Error: "+jsonObj.error);
			}
		}
	});	
}

/**
	@class Useful methods
*/
function Utilities() {
	/**
	* Defines xmlhttp for all browsers
	*/
	this.ajaxFunction = function () {
		var xmlHttp;
		try {
			// Firefox, Opera 8.0+, Safari
			xmlHttp = new XMLHttpRequest();
		}
		catch (e1) { // Internet Explorer
			try {
				xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e2) {
				try {
					xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e3) {
					alert("Optim:ajaxFunction: Your browser does not support AJAX.");
				return false;
				}
			}
		}
		return xmlHttp;
	};
	this.returnObjById = function (id) {
		var returnVar;
		if (document.getElementById) {
			returnVar = document.getElementById(id);
		}
		else if (document.all) {
			returnVar = document.all[id];
		}
		else if (document.layers) {
			returnVar = document.layers[id];
		}
		return returnVar;
	};

	this.fixDisplayText = function (string) {
		return string.replace(/\r\n|\r|\n|\n\r/g , '<br />');
	};	
}

/**
	@class Handles response to user input as directed by HandleEvents
*/
function Localization () {
	var index;
	this.version = "6.1.0:2009-06-11";
	// Check for the various File API support.
	
	// Controls
	this.util = new Utilities();
	
//	this.cancelTranslation = new CancelTranslation("CancelTranslation", this.util);
	this.selectPrompt = new SelectPrompt("SelectPrompt", this.util);
	this.updateTranslation = new UpdateTranslation("UpdateTranslation", this.util);

	this.someTextAreaChanged = false;
	
	this.selectPromptOnChange = function () {
		this.selectPrompt.onChange(this);
	};
	this.updateTranslationOnKeyPress = function (tid) {
		this.someTextAreaChanged = true;
		this.updateTranslation.onKeyPress(tid);
	};
	this.updateTranslationOnClick = function (tid) {
		this.updateTranslation.onClick(tid);
		this.someTextAreaChanged = false;
	};
}
/**
	Handles user activity on web page
*/
function handleEvent(type, parameter) {
	switch (type) {
		case "init":
			localization = new Localization(); // This is global, hopefully the only global.
			return this;
			break;
		case "selectPrompt":
			localization.selectPromptOnChange();
			break;
		case "translationTextChanged":
			localization.updateTranslationOnKeyPress(parameter);
			break;
		case "updateTranslation":
			localization.updateTranslationOnClick(parameter);
			break;
		default:
			alert("handleEvents: Type not found: " + type );
			break; 
	}
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



