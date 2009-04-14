<?php

/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once('MDB2.php');
require_once('version.php');

if(file_exists("config.php")) {
	die("A configuration file already exists. Please delete <i>config.php</i> if you wish to reinstall.");
}

if (isset($_POST['install'])) {

	//Get the database connection string
	$dbms = $_POST['dbms'];
	if($dbms == "sqlite") {
		$filename = $_POST['filename'];
		$connect_string = "sqlite:///" . $filename;
	} else {
		$connect_string = $dbms . "://" . $_POST['username'] . ":" . $_POST['password'] . "@" . $_POST['hostname'] . ":" . $_POST['port'] . "/" . $_POST['dbname'];
	}

	$mdb2 =& MDB2::connect($connect_string);
	if (PEAR::isError($mdb2)) {
		die($mdb2->getMessage());
	}

	//Create tables

	/* Suggested additions to the Users table:
	 *
	 *   webid VARCHAR(255)
	 *      - stores the WebID for the user. see http://esw.w3.org/topic/WebID
	 *      - web/data/User.php already uses this field if present
	 *
	 *   avatar VARCHAR(255) 
	 *      - stores a URI for the person's avatar / photo.
	 *      - will need a small mod to web/data/User.php once added.
	 */

	$mdb2->query("CREATE TABLE Users (username VARCHAR(64) PRIMARY KEY,
		password VARCHAR(32) NOT NULL,
		email VARCHAR(255),
		fullname VARCHAR(255),
		bio TEXT,
		homepage VARCHAR(255),
		location VARCHAR(255),
		created int NOT NULL,
		modified int,
		userlevel int DEFAULT 0);");

	$res = $mdb2->query("CREATE TABLE Auth (token VARCHAR(32) PRIMARY KEY,
		sk VARCHAR(32),
		expires int,
		username VARCHAR(64) REFERENCES Users(username))");

	$mdb2->query("CREATE TABLE Artist(
		name VARCHAR(255) PRIMARY KEY,
		mbid VARCHAR(36),
		streamable int,
		bio_published int,
		bio_content TEXT,
		bio_summary TEXT,
		image_small VARCHAR(255),
		image_medium VARCHAR(255),
		image_large VARCHAR(255),
		homepage VARCHAR(255));");

	$mdb2->query("CREATE TABLE Album(
		name VARCHAR(255),
		artist_name VARCHAR(255) REFERENCES Artist(name),
		mbid VARCHAR(36),
		image VARCHAR(255),
		artwork_license VARCHAR(255),
		releasedate int,
		albumurl VARCHAR(255),
		downloadurl VARCHAR(255),
		PRIMARY KEY(name, artist_name));");

	// Table for registering similar artists
	$mdb2->query("CREATE TABLE Similar_Artist(
		name_a VARCHAR(255) REFERENCES Artist(name),
		name_b VARCHAR(255) REFERENCES Artist(name),
		PRIMARY KEY(name_a, name_b))");

	$mdb2->query("CREATE TABLE Track(
		name VARCHAR(255),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255) REFERENCES Album(name),
		mbid VARCHAR(36),
		duration int,
		streamable int,
		license VARCHAR(255),
		downloadurl VARCHAR(255),
		streamurl VARCHAR(255),
		otherid VARCHAR(16),
		PRIMARY KEY(name, artist));");

	$mdb2->query("CREATE TABLE Scrobbles(
		username VARCHAR(64) REFERENCES Users(username),
		track VARCHAR(255) REFERENCES Track(name),
		album VARCHAR(255) REFERENCES Album(name),
		artist VARCHAR(255) REFERENCES Artist(name),
		time int,
		mbid VARCHAR(36),
		source VARCHAR(6),
		rating CHAR(1),
		length INT(4),
		PRIMARY KEY(username, track, artist, time))");

	$mdb2->query("CREATE TABLE Scrobble_Sessions(
		username VARCHAR(64) REFERENCES Users(username),
		sessionid VARCHAR(32),
		client CHAR(3),
		expires int,
		PRIMARY KEY(username, sessionid))");

	$res = $mdb2->query("CREATE TABLE Now_Playing(
		sessionid VARCHAR(32) PRIMARY KEY REFERENCES Scrobble_Sessions(sessionid),
		track VARCHAR(255) REFERENCES Track(name),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255) REFERENCES Album(name),
		mbid VARCHAR(36),
		expires int)");

	$res = $mdb2->query("CREATE TABLE Invitation_Request(
		email VARCHAR(255) PRIMARY KEY,
		time int");

	$res = $mdb2->query("CREATE TABLE Invitations(
		inviter VARCHAR(64) REFERENCES Users(username),
		invitee VARCHAR(64) REFERENCES Users(username),
		code VARCHAR(32),
		PRIMARY KEY(inviter, invitee, code))");

	$res = $mdb2->query("CREATE TABLE ClientCodes(
		code CHAR(3),
		name VARCHAR(32),
		url VARCHAR(256),
		PRIMARY KEY(code))");

	$res = $mdb2->query("CREATE TABLE Tags(
		username VARCHAR(64) REFERENCES Users(username),
		tag VARCHAR(64),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255) REFERENCES Album(name),
		track VARCHAR(255) REFERENCES Track(name),
		PRIMARY KEY(username, tag, artist, album, track))");

	// Test user configuration
	$res = $mdb2->query("INSERT INTO Users
		(username, password, created)
		VALUES
		('testuser', '" . md5('password') . "', " . time() . ")");
	
	$mdb2->disconnect();

	$submissions_server = $_POST['submissions'];
	$install_path = dirname(__FILE__) . "/";

	//Write out the configuration
	$config = "<?php \$config_version = " . $version .";\n \$connect_string = '" . $connect_string . "';\n \$submissions_server = '" . $submissions_server . "';\n\$install_path = '" . $install_path . "'; ?>";

	$conf_file = fopen("config.php", "w");
	$result = fwrite($conf_file, $config);
	fclose($conf_file);

	if(!$result) {
		$print_config = str_replace("<", "&lt;", $config);
		die("Unable to write to file '<i>config.php</i>'. Please create this file and copy the following in to it: <br /><pre>" . $print_config . "</pre>");
	}	

	die("Configuration completed successfully!");
}

?>
<html>
	<head>
		<title>Gobbler Installer</title>
		<script type='text/javascript'>
			function showSqlite() {
				document.getElementById("sqlite").style.visibility = "visible";
				document.getElementById("networkdbms").style.visibility = "hidden";
			}

			function showNetworkDBMS() {
				document.getElementById("sqlite").style.visibility = "hidden";
				document.getElementById("networkdbms").style.visibility = "visible";
			}
		</script>
	</head>

	<body onload="showSqlite()">
		<h1>Gobbler Installer</h1>
		<form method="post">
			<h2>Database</h2>
			Database Management System: <br />
			<input type="radio" name="dbms" value="sqlite" onclick='showSqlite()' checked>SQLite (use an absolute path)</input><br />
			<input type="radio" name="dbms" value="mysql" onclick='showNetworkDBMS()'>MySQL</input><br />
			<input type="radio" name="dbms" value="pgsql" onclick='showNetworkDBMS()'>PostgreSQL</input><br />
			<br />
			<div id="sqlite">
				Filename: <input type="text" name="filename" /><br />
			</div>
			<div id="networkdbms">
				Hostname: <input type="text" name="hostname" /><br />
				Port: <input type="text" name="port" /><br />
				Database: <input type="text" name="dbname" /><br />
				Username: <input type="text" name="username" /><br />
				Password: <input type="password" name="password" /><br />
			</div>
			<br />
			<h2>Servers</h2>
			Submissions Server URL: <input type="text" name="submissions" value="http://localhost/" /><br />
			<br />
			<input type="submit" value="Install" name="install" />
		</form>
	</body>
</html>


