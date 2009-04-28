<?php

/* Just adding this temporarily to bootstrap groups. Gone soon. */

require_once('../database.php');

//    $res = $mdb2->query("CREATE TABLE Groups (
//    	groupname VARCHAR(64) PRIMARY KEY,
//    	owner VARCHAR(64) REFERENCES Users(username),
//    	fullname VARCHAR(255),
//    	bio TEXT,
//    	homepage VARCHAR(255),
//    	created int NOT NULL,
//    	modified INTEGER,
//    	avatar_uri VARCHAR(255),
//    	grouptype INTEGER)");

$mdb2->query("INSERT INTO Groups VALUES ('dev', 'mattl', 'libre.fm Developers', 'Developers of gnukebox, nixtape and libre.fm', 'http://ideas.libre.fm/', 1240945188, 1240945188, NULL, 0);");
$mdb2->query("INSERT INTO Groups VALUES ('altrock', 'tobyink', 'alt.rock', 'Grunge, indie, Britpop, Americana and more!', 'http://en.wikipedia.org/wiki/Alternative_rock', 1240945288, 1240945288, NULL, 0);");

//    $res = $mdb2->query("CREATE TABLE Group_Members (
//    	groupname VARCHAR(64) REFERENCES Groups(groupname),
//    	member VARCHAR(64) REFERENCES Users(username),
//    	joined int NOT NULL");

$mdb2->query("INSERT INTO Group_Members VALUES ('dev', 'mattl', 1240945188);");
$mdb2->query("INSERT INTO Group_Members VALUES ('dev', 'tobyink', 1240945189);");
$mdb2->query("INSERT INTO Group_Members VALUES ('dev', 'elleo', 1240945190);");
$mdb2->query("INSERT INTO Group_Members VALUES ('altrock', 'tobyink', 1240945288);");
