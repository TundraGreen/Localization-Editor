Localization Editor
/*
Copyright © 2012 William H. Prescott. All Rights Reserved.

This file is part of Localization Editor.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

3. The name of the author may not be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY William H. Prescott "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

Version history
---------------
Version 1.1 (Build 2)
	Add config.php file with a list of known localization databases.
	Added drop down list of databases on index page.
	These changes makes it easier to maintain several different localization databases.
Version 1.0.1 (Build 1)
	Simplified project directory structure
	Simplified and modified build.xml
	Created develop branch for future development effort.
v. 1.0


Overview
--------
A system for maintaining a localization database. An application inserts "prompts" or "keys" where ever a language specific string is required. On execution, the application pulls the actual string in the appropriate language from the database. 

Localization Editor is a web app that makes it easy to create and maintain the database. It was particularly designed to facilitate multi-language web pages, but could be used anywhere support for various languages is desired. The maintenance of the text is completely separated from 
maintenance of the source code and can be done by translators with no knowledge of the source code.
 
The Editor uses php, javascript and ajax to manage the localization information which is stored in an sqlite database. The localized text strings are stored in a database. When an application needs a text string it is pulled from the data base using the prompt and language code as keys.

For example a web page would include the string:
<?php print local("hello_world") ?>
Where "local" is a function that queries the localization database using the string "hello_world" and a stored language cookie.

local might return "Hello World", or "Hola Mundo", or "Hallo Welt".


System components
-----------------
LocalizationEditor/index.php
	A web app that is used to add or edit entries in the database.
phpUtils.php
	A few php functions used by the Localization Editor
le.css
	The css used by the editor
le.js
	The javascript used by the editor
localization.sqlite
	A database containing
		Table of language-country pairs
		Table of prompts or keywords
		Table of text
addLanguage.php
	responds to an ajax call and adds a new language code
addPrompt.php
	responds to an ajax call and adds a new prompt
updateTranslation.php
	responds to an ajax call and modifies a stored localized text string
Cookie.php
	Checks for the presence of a language cookie
Several Apache ant build.xml and config files are included for zipping or pushing to a local web server.

Database structure
------------------
DROP TABLE languages;
CREATE TABLE languages
(
lid INTEGER PRIMARY KEY,
langcode TEXT NOT NULL DEFAULT ''
);
INSERT INTO languages (langcode) VALUES ("en_US");
INSERT INTO languages (langcode) VALUES ("es_MX");

DROP TABLE prompts;
CREATE TABLE prompts
(
pid INTEGER PRIMARY KEY,
promptstring TEXT NOT NULL DEFAULT ''
);
INSERT INTO prompts (promptstring) VALUES ("name");
INSERT INTO prompts (promptstring) VALUES ("address");

DROP TABLE translations;
CREATE TABLE translations
(
tid INTEGER PRIMARY KEY,
langid INTEGER NOT NULL DEFAULT 0,
promptid INTEGER NOT NULL DEFAULT 0,
langstring TEXT NOT NULL DEFAULT ''
);
INSERT INTO translations (langid, promptid, langstring) VALUES (1, 1, "Name");
INSERT INTO translations (langid, promptid, langstring) VALUES (2, 1, "Nombre");
INSERT INTO translations (langid, promptid, langstring) VALUES (1, 2, "Address");
INSERT INTO translations (langid, promptid, langstring) VALUES (2, 2, "Direcci&oacute;n");
