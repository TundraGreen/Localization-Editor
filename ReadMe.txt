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

Overview
--------
A system for maintaining a localization database. An application inserts "prompts" or "keys" where ever a language specific string is required. On execution, the application pulls that actual string in the appropriate language from the database. 

Localization Editor is a web app that makes it easy to create and maintain the database. It was particularly designed to facilitate multi-language web pages, but could be used anywhere support for various languages is desired. The maintenance of the text is completely separated from 
maintenance of the source code and can be done by translators with no knowledge of the source code.
 
The Editor uses php, javascript and ajax to manage the localization information which is stored in an sqlite database. The text strings from the web pages are stored in a database. When the page is delivered to the user, php decides which language to pull from the database.

The text is included in the web page with a entry like:
<?php print local("hello_world") ?>
When the page is requested, the php looks up the string, "hello_world" in the database and returns, for example, either "Hello World", or "Hola a Mundo", or "Hallo Welt".


System components
-----------------
LocalizationEditor.php
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
