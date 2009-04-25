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

	$res = $mdb2->query("CREATE TABLE Places(
		location_uri VARCHAR(255) unique,
		latitude FLOAT,
		longitude FLOAT,
		country CHAR(2));");

	$mdb2->query("CREATE TABLE Users (
		username VARCHAR(64) PRIMARY KEY,
		password VARCHAR(32) NOT NULL,
		email VARCHAR(255),
		fullname VARCHAR(255),
		bio TEXT,
		homepage VARCHAR(255),
		location VARCHAR(255),
		created int NOT NULL,
		modified INTEGER,
		userlevel INTEGER DEFAULT 0,
		webid_uri VARCHAR(255),
		avatar_uri VARCHAR(255),
		active integer DEFAULT 1,
		location_uri VARCHAR(255) REFERENCES Places(location_uri));");

	$mdb2->query("CREATE TABLE AccountActivation(
		username VARCHAR(64),
		authcode VARCHAR(32))");

	$res = $mdb2->query("CREATE TABLE Auth (
		token VARCHAR(32) PRIMARY KEY,
		sk VARCHAR(32),
		expires INTEGER,
		username VARCHAR(64) REFERENCES Users(username))");

	$mdb2->query("CREATE TABLE Artist(
		name VARCHAR(255) PRIMARY KEY,
		mbid VARCHAR(36),
		streamable INTEGER,
		bio_published INTEGER,
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
		releasedate INTEGER,
		albumurl VARCHAR(255),
		downloadurl VARCHAR(255));");

	// Table for registering similar artists
	$mdb2->query("CREATE TABLE Similar_Artist(
		name_a VARCHAR(255) REFERENCES Artist(name),
		name_b VARCHAR(255) REFERENCES Artist(name),
		PRIMARY KEY(name_a, name_b));");

	$mdb2->query("CREATE TABLE Track(
		id INTEGER NOT NULL DEFAULT nextval('track_id_seq'::regclass) PRIMARY KEY,
		name VARCHAR(255),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255),
		mbid VARCHAR(36),
		duration INTEGER,
		streamable INTEGER,
		license VARCHAR(255),
		downloadurl VARCHAR(255),
		streamurl VARCHAR(255),
		otherid VARCHAR(16));");

	$mdb2->query("CREATE TABLE Scrobbles(
		username VARCHAR(64) REFERENCES Users(username),
		track VARCHAR(255),
		album VARCHAR(255),
		artist VARCHAR(255) REFERENCES Artist(name),
		time INTEGER,
		mbid VARCHAR(36),
		source VARCHAR(6),
		rating CHAR(1),
		length INTEGER);");

	$mdb2->query("CREATE TABLE Scrobble_Sessions(
		username VARCHAR(64) REFERENCES Users(username),
		sessionid VARCHAR(32) PRIMARY KEY,
		client CHAR(3),
		expires INTEGER);");

	$res = $mdb2->query("CREATE TABLE Now_Playing(
		sessionid VARCHAR(32) PRIMARY KEY REFERENCES Scrobble_Sessions(sessionid),
		track VARCHAR(255),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255),
		mbid VARCHAR(36),
		expires INTEGER);");

	$res = $mdb2->query("CREATE TABLE Invitation_Request(
		email VARCHAR(255) PRIMARY KEY,
		time INTEGER);");

	$res = $mdb2->query("CREATE TABLE Invitations(
		inviter VARCHAR(64) REFERENCES Users(username),
		invitee VARCHAR(64) REFERENCES Users(username),
		code VARCHAR(32),
		PRIMARY KEY(inviter, invitee, code));");

	$res = $mdb2->query("CREATE TABLE ClientCodes(
		code CHAR(3),
		name VARCHAR(32),
		url VARCHAR(256),
		free CHAR(1),
		PRIMARY KEY(code));");

	$res = $mdb2->query("CREATE TABLE Tags(
		username VARCHAR(64) REFERENCES Users(username),
		tag VARCHAR(64),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255),
		track VARCHAR(255));");

	$res = $mdb2->query("CREATE TABLE Error(
		    id int(11) AUTO_INCREMENT KEY,
		    msg text,
		    data text,
		    time int);");
	$res = $mdb2->query("CREATE TABLE Recovery_Request(
		    username VARCHAR(64),
		    email VARCHAR(255),
		    code VARCHAR(32),
		    expires int, 
		    PRIMARY KEY(username));");

// uncomment these to solve performance problems with getRecentScrobbles
// 	$res = $mdb2->exec("CREATE INDEX album_artistname_idx ON Album(artist_name)");
// 	$res = $mdb2->exec("CREATE INDEX scrobbles_artist_idx ON Scrobbles(artist)");
//	$res = $mdb2->exec("CREATE INDEX scrobbles_time_idx ON Scrobbles(time)");

// uncomment these if you're using postgresql and want to run the software as www-data
//	$res = $mdb2->exec("GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Album, Artist, Auth, Clientcodes, Error, Invitation_Request, Invitations, Now_Playing, Places, Radio_Sessions, Scrobble_Sessions, Scrobbles, Similar_artist, Tags, Track, Users to \"www-data\"");
//	$res = $mdb2->exec("GRANT SELECT, UPDATE ON users_uniqueid_seq to \"www-data\"");

	// Test user configuration
	$res = $mdb2->query("INSERT INTO Users
		(username, password, created)
		VALUES
		('testuser', '" . md5('password') . "', " . time() . ");");

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
		<title>GNUkebox Installer</title>
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
		<h1>GNUkebox Installer</h1>
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


