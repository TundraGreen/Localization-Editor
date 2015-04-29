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
Version 1.7 (Build 41) 2015-04-29
  Minor cleanup: remove debug, change require_once to include_once.
Version 1.7 (Build 40) 2015-04-29
  Fixed bug in handling of consecutive prompts like:
    //         active_record.errors.models.hospital.attributes.data_center:
    //         active_record.errors.models.incidents.attributes.data_center:
  Also tweaked display, creating Admin Only coloring.
Version 1.7 (Build 39) 2015-04-28
  Finished adding yaml support.
Version 1.7 (Build 38) 2015-04-28
  Add support for reading and writing Beacon v.2 yaml files.
Version 1.6.1 (Build 37) 2013-06-19
	Change to exclude all dot files when reading language files.
	modified:   ReadMe.txt
	modified:   source/ajax/readLanguageFiles.php
Version 1.6.1 (Build 36) 2013-06-19
	Fixed handling of quote characters in translations
	Single quotes in strings are doubled in the database
	Double quotes are unchanged in the datase
	Single and double quotes are \ escaped in the language files
	Both appear normally in the editor
	modified:   ReadMe.txt
	modified:   source/ajax/readLanguageFiles.php
	modified:   source/ajax/writeLanguageFiles.php
Version 1.6.1 (Build 35) 2013-06-19
	Minor cleanup, no significant changes
	modified:   ReadMe.txt
	modified:   source/ajax/readLanguageFiles.php
	modified:   source/ajax/writeLanguageFiles.php
	modified:   source/index.php
Version 1.6.1 (Build 34) 2013-06-18
	Converted to use double quotes instead of single quotes in language files
	modified:   ReadMe.txt
	modified:   source/ajax/addDatabase.php
	modified:   source/ajax/addLanguage.php
	modified:   source/ajax/addPrompt.php
	modified:   source/ajax/readLanguageFiles.php
	modified:   source/ajax/updateTranslation.php
	modified:   source/ajax/writeLanguageFiles.php
	modified:   source/index.php
	modified:   source/javascript/le.js
Version 1.6 (Build 33) 2013-06-18
	Added capability of building database from language files.
	new file:   source/ajax/readLanguageFiles.php
	modified:   ReadMe.txt
	modified:   source/index.php
	modified:   source/javascript/le.js
Version 1.5 (Build 32) 2013-05-27
	Add single quotes around associative array key.
Version 1.5 (Build 31) 2013-05-27
	Comment out unused line that was producing error (uid).
Version 1.5 (Build 30) 2013-05-27
	Add single quotes around associative array key.
	Missed one last time.
Version 1.5 (Build 29) 2013-05-27
	Added jquery scrollto to git.
Version 1.5 (Build 28) 2013-05-27
	Add single quotes around associative array keys.
	modified:   ReadMe.txt
	modified:   source/index.php
Version 1.5 (Build 27) 2013-01-05
	Sorted out and cleaned up encoding of text strings.
	When storing:
	They are encodeURI by javascript before sending to php
	Then php urldecodes them before sending to sqlite
	sqlite automatically adds backslashes to single quotes
	When displaying in editor:
	php stripslashes strips slashes from them.
	When writing language files:
	php leaves the backslashed single quotes backslashed
Version 1.4 (Build 26) 2013-01-05
	Fixed prompt box to scroll to current prompt using Flesler's jquery addon scrollto.
Version 1.4 (Build 25) 2012-12-13
	Add alert to provide user feedback that files have been written.
Version 1.4 (Build 24) 2012-12-13
	Fix error in writeLanguageFiles.php
	Reduce size of text boxes.
Version 1.4 (Build 23) 2012-12-11
	Minor cosmetic changes to web appearance.
Version 1.4 (Build 22) 2012-12-06
	Changed order of languages to alphabetical
	Set pid, so some prompt is selected the first time.
Version 1.4 (Build 21) 2012-11-24
	Deleted obsolete file.
Version 1.4 (Build 20) 2012-11-24
	Simplified javascript version utilizing jquery libraries
	jquery, jquery.cookie, jquery.ui
Version 1.3 (Build 19) 2012-11-23
	All code converted to jquery except selectPrompt.
Version 1.3 (Build 18) 2012-11-23
	Cleaning dead code from le.js
Version 1.3 (Build 17) 2012-11-23
	Cleaning dead code from le.js
Version 1.3 (Build 16)
	Convert AddLanguage and AddPrompt to jquery ajax.
Version 1.3 (Build 15)
Version 1.3 (Build 14)
	Fixed bug that could occur if active database is removed
	so cookie points at nonexistent database.
Version 1.3 (Build 13)
	Fixed bug in writeLanguageFiles.php
	It had not been updated to use dbName cookie.
Version 1.3 (Build 12)
	When a new database is created,
	make it the active database.
Version 1.3 (Build 11)
	Updated Overview to explain recent additions:
	Multiple databases
	Language files
Version 1.3 (Build 10)
	Fixed minor bug.
	After new prompt is added, it is now selected.
Version 1.3 (Build 9)
	Includes option to create a new database.
Version 1.2.1 (Build 8)
	Intermediate build, runs but not complete.
Version 1.2.1 (Build 7)
	Debug new location of databases.
	Partially implement code to add a new database.
Version 1.2 (Build 6)
	Updated addLanguage.php and addPrompt.php and updateTranslation.php
	to find database in new place.
Version 1.2 (Build 5)
	Write translated strings to a set of language files.
	User can access the translations either:
		Directly from the database with a local() function
		Or as php defines with the language files.
Version 1.1 (Build 4)
	Partial implementation of code to write language strings to files.
Version 1.1 (Build 3)
	Partial implementation of code to write language strings to files.
Version 1.1 (Build 2)
	Add config.php file with a list of known localization databases.
	Added drop down list of databases on index page.
	These changes make it easier to maintain several different localization databases.
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

2023-11-22
The Editor has been updated to handle multiple localization databases more smoothly. In the original implementation it operated on a single database location and it was up to the user to swap the databases around if there was more than one.

Now it stores the localization info in subfolders under a Databases folder. Each subfolder contains an sqlite file with the localization info. There is now a dropdown menu to allow switching between the options in Databases. In addition it is now possible to write all the translations strings to files, with one file for each language. This allows use of the localization with out any database calls. The user can just point the pages at the appropriate language. For US english, include the en_US.php file, for Spanish, the es_MX.php file. Each language file contains php define statements for all the prompts. To use them simply include the appropriate language file, then <?php print hello_world ?> will result in "Hello World" or "Hola Mundo" depending on which file has been included.


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

DROP TABLE prompts;
CREATE TABLE prompts
(
pid INTEGER PRIMARY KEY,
promptstring TEXT NOT NULL DEFAULT ''
);

DROP TABLE translations;
CREATE TABLE translations
(
tid INTEGER PRIMARY KEY,
langid INTEGER NOT NULL DEFAULT 0,
promptid INTEGER NOT NULL DEFAULT 0,
langstring TEXT NOT NULL DEFAULT ''
);
INSERT INTO languages (langcode) VALUES ("en_US");
INSERT INTO languages (langcode) VALUES ("es_MX");
INSERT INTO prompts (promptstring) VALUES ("name");
INSERT INTO prompts (promptstring) VALUES ("address");
INSERT INTO translations (langid, promptid, langstring) VALUES (1, 1, "Name");
INSERT INTO translations (langid, promptid, langstring) VALUES (2, 1, "Nombre");
INSERT INTO translations (langid, promptid, langstring) VALUES (1, 2, "Address");
INSERT INTO translations (langid, promptid, langstring) VALUES (2, 2, "Direcci&oacute;n");
