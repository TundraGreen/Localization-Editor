Localization Editor

Copyright 2012 William Prescott


v 1.0 2012-03-29

Overview
--------
A system for maintaining multiple languages on a web page using php
and sqlite. The text strings from the web pages are stored in a database.
When the page is delivered to the user, php decides which language to 
pull from the database.

The text is included in the web page with a entry like:
<?php print local("hello_world") ?>
When the page is requested, the php looks up the string, "text_prompt"
in the database and returns, either "Hello World", or "Hola a Mundo", or 
"Hallo Welt".


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
