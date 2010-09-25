<?php

/* GNUkebox -- a free software server for recording your listening habits

   Copyright (C) 2009 Free Software Foundation, Inc

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

require_once('adodb/adodb-exceptions.inc.php');
require_once('adodb/adodb.inc.php');
require_once('version.php');
require_once('utils/get_absolute_url.php');

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
	
	$adodb_connect_string = str_replace('pgsql:', 'postgres:', $connect_string );

	try {
		$adodb =& NewADOConnection($adodb_connect_string);
	} catch (exception $e) {
		var_dump($e);
		adodb_backtrace($e->gettrace());
		die("Database connection failure\n");
	}

	//Create tables

	$adodb->Execute("CREATE TABLE Places(
		location_uri VARCHAR(255) unique,
		latitude FLOAT,
		longitude FLOAT,
		country CHAR(2))");

	$adodb->Execute("CREATE TABLE Countries (
		country varchar(2) PRIMARY KEY,
		country_name varchar(200),
		wikipedia_en varchar(120));");

	$adodb->Execute("CREATE TABLE Users (
		uniqueid SERIAL PRIMARY KEY,
		username VARCHAR(64) unique,
		password VARCHAR(32) NOT NULL,
		email VARCHAR(255),
		fullname VARCHAR(255),
		bio TEXT,
		homepage VARCHAR(255),
		location VARCHAR(255),
		userlevel INTEGER DEFAULT 0,
		anticommercial INTEGER DEFAULT 0,
		webid_uri VARCHAR(255),
		avatar_uri VARCHAR(255),
		openid_uri VARCHAR(100),
		active INTEGER DEFAULT 0,
		public_export INTEGER DEFAULT 0,
		location_uri VARCHAR(255) REFERENCES Places(location_uri),
		laconica_profile VARCHAR(255),
		created INTEGER DEFAULT 0,
		modified INTEGER DEFAULT 0,
		journal_rss VARCHAR(255))");

	$adodb->Execute("CREATE TABLE Groups (
		id SERIAL PRIMARY KEY,
		groupname VARCHAR(64),
		owner INTEGER REFERENCES Users(uniqueid),
		fullname VARCHAR(255),
		bio TEXT,
		homepage VARCHAR(255),
		created INTEGER NOT NULL,
		modified INTEGER,
		avatar_uri VARCHAR(255),
		grouptype INTEGER)");

	$adodb->Execute("CREATE TABLE Group_Members (
		grp INTEGER REFERENCES Groups(id),
		member INTEGER REFERENCES Users(uniqueid),
		joined INTEGER NOT NULL,
		PRIMARY KEY (grp, member))");

	$adodb->Execute("CREATE TABLE AccountActivation(
		username VARCHAR(64),
		authcode VARCHAR(32),
		expires INTEGER)");

	$adodb->Execute("CREATE TABLE Auth (
		token VARCHAR(32) PRIMARY KEY,
		sk VARCHAR(32),
		expires INTEGER,
		username VARCHAR(64) REFERENCES Users(username))");

	$adodb->Execute("CREATE TABLE Artist(
		id SERIAL PRIMARY KEY,
		name VARCHAR(255) unique,
		mbid VARCHAR(36),
		streamable INTEGER,
		bio_published INTEGER,
		bio_content TEXT,
		bio_summary TEXT,
		image_small VARCHAR(255),
		image_medium VARCHAR(255),
		image_large VARCHAR(255),
		homepage VARCHAR(255),
		origin VARCHAR(255) REFERENCES Places(location_uri))");

	$adodb->Execute("CREATE TABLE Album(
		id SERIAL PRIMARY KEY,
		name VARCHAR(255),
		artist_name VARCHAR(255) REFERENCES Artist(name),
		mbid VARCHAR(36),
		image VARCHAR(255),
		artwork_license VARCHAR(255),
		releasedate INTEGER,
		albumurl VARCHAR(255),
		downloadurl VARCHAR(255))");

	// Table for registering similar artists
	$adodb->Execute("CREATE TABLE Similar_Artist(
		name_a VARCHAR(255) REFERENCES Artist(name),
		name_b VARCHAR(255) REFERENCES Artist(name),
		PRIMARY KEY(name_a, name_b))");

	if ( strtolower(substr($dbms,0,5)) == 'mysql'  ) {
		$adodb->Execute("CREATE TABLE Track(
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(255),
			artist_name VARCHAR(255) REFERENCES Artist(name),
			album_name VARCHAR(255),
			mbid VARCHAR(36),
			duration INTEGER,
			streamable INTEGER DEFAULT 0,
			license VARCHAR(255),
			downloadurl VARCHAR(255),
			streamurl VARCHAR(255),
			otherid VARCHAR(16))");
	} else {
		$adodb->Execute("CREATE SEQUENCE track_id_seq;");
		$adodb->Execute("CREATE TABLE Track(
			id INTEGER NOT NULL DEFAULT nextval('track_id_seq'::regclass) PRIMARY KEY,
			name VARCHAR(255),
			artist_name VARCHAR(255) REFERENCES Artist(name),
			album_name VARCHAR(255),
			mbid VARCHAR(36),
			duration INTEGER,
			streamable INTEGER DEFAULT 0,
			license VARCHAR(255),
			downloadurl VARCHAR(255),
			streamurl VARCHAR(255),
			otherid VARCHAR(16))");
	}

	$adodb->Execute("CREATE TABLE Scrobbles(
		userid INTEGER REFERENCES Users(uniqueid),
		track VARCHAR(255),
		album VARCHAR(255),
		artist VARCHAR(255) REFERENCES Artist(name),
		time INTEGER,
		mbid VARCHAR(36),
		source VARCHAR(6),
		rating CHAR(1),
		length INTEGER,
		stid INTEGER)");

	$adodb->Execute("CREATE TABLE Scrobble_Sessions(
		userid INTEGER REFERENCES Users(uniqueid),
		sessionid VARCHAR(32) PRIMARY KEY,
		client CHAR(3),
		expires INTEGER)");

	$adodb->Execute("CREATE TABLE Now_Playing(
		sessionid VARCHAR(32) PRIMARY KEY REFERENCES Scrobble_Sessions(sessionid) ON DELETE CASCADE,
		track VARCHAR(255),
		artist VARCHAR(255),
		album VARCHAR(255),
		mbid VARCHAR(36),
		expires INTEGER)");

	$adodb->Execute("CREATE TABLE Invitation_Request(
		email VARCHAR(255) PRIMARY KEY,
		time INTEGER)");

	$adodb->Execute("CREATE TABLE Invitations(
		inviter VARCHAR(64) REFERENCES Users(username),
		invitee VARCHAR(64) REFERENCES Users(username),
		code VARCHAR(32),
		PRIMARY KEY(inviter, invitee, code))");

	$adodb->Execute("CREATE TABLE ClientCodes(
		code CHAR(3),
		name VARCHAR(32),
		url VARCHAR(256),
		free CHAR(1),
		PRIMARY KEY(code))");

	$adodb->Execute("CREATE TABLE Tags(
		tag VARCHAR(64),
		artist VARCHAR(255) REFERENCES Artist(name),
		album VARCHAR(255),
		track VARCHAR(255),
		userid INTEGER REFERENCES Users(uniqueid),
		UNIQUE(tag, artist, album, track, userid))");

	$adodb->Execute("CREATE TABLE Manages(
		userid INTEGER REFERENCES Users(uniqueid),
		artist VARCHAR(255) REFERENCES Artist(name),
		authorised INTEGER)");

	$adodb->Execute("CREATE TABLE Error(
			id SERIAL PRIMARY KEY,
			msg TEXT,
		    data TEXT,
		    time INTEGER)");
	$adodb->Execute("CREATE TABLE Recovery_Request(
		    username VARCHAR(64),
		    email VARCHAR(255),
		    code VARCHAR(32),
		    expires INTEGER,
		    PRIMARY KEY(username))");

	$adodb->Execute("CREATE TABLE Radio_Sessions(
			username VARCHAR(64),
			session VARCHAR(32),
			url VARCHAR(255),
			expires INTEGER NOT NULL DEFAULT 0,
			PRIMARY KEY(session))");

	//Table for delete profile requests
	$adodb->Execute("CREATE TABLE Delete_Request (
			code VARCHAR(300),
			expires INTEGER,
			username VARCHAR(64) REFERENCES Users(username),
			PRIMARY KEY(code))");

	$adodb->Execute("CREATE TABLE Scrobble_Track(
			id SERIAL PRIMARY KEY,
			artist VARCHAR(255) NOT NULL,
			album VARCHAR(255),
			name VARCHAR(255) NOT NULL,
			mbid VARCHAR(36),
			track INTEGER NOT NULL)");

	$adodb->Execute("CREATE VIEW Free_Scrobbles AS
			SELECT s.userid, s.track, s.artist, s.time, s.mbid, s.album, s.source, s.rating, s.length
				FROM Scrobbles s
				JOIN Scrobble_Track st ON s.stid = st.id
				JOIN Track t ON st.track = t.id
				WHERE t.streamable = 1");

	$adodb->Execute("CREATE TABLE Banned_Tracks (
		userid INTEGER REFERENCES Users(uniqueid) ON DELETE CASCADE,
		track varchar(255),
		artist varchar(255),
		time INTEGER,
		UNIQUE(userid, track, artist))");

	$adodb->Execute("CREATE TABLE Loved_Tracks (
		userid INTEGER REFERENCES Users(uniqueid) ON DELETE CASCADE,
		track varchar(255),
		artist varchar(255),
		time varchar(255),
		UNIQUE(userid, track, artist))");

	$adodb->Execute("CREATE TABLE User_Relationships (
		uid1 INTEGER REFERENCES Users(uniqueid) ON DELETE CASCADE,
		uid2 INTEGER REFERENCES Users(uniqueid) ON DELETE CASCADE,
		established INTEGER NOT NULL,
		PRIMARY KEY (uid1, uid2))");
	
	$adodb->Execute("CREATE TABLE Relationship_Flags (
		flag VARCHAR(12),
		PRIMARY KEY (flag))");

	$adodb->Execute("CREATE TABLE User_Relationship_Flags (
		uid1 INTEGER,
		uid2 INTEGER,
		flag VARCHAR(12) REFERENCES Relationship_Flags(flag),
		PRIMARY KEY (uid1, uid2, flag),
		FOREIGN KEY (uid1, uid2) REFERENCES User_Relationships (uid1, uid2))");
	
	
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('contact')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('acquaintance')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('friend')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('met')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('co-worker')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('colleague')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('co-resident')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('neighbor')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('child')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('parent')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('sibling')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('spouse')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('kin')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('muse')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('crush')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('date')");
	$adodb->Execute("INSERT INTO Relationship_Flags VALUES ('sweetheart')");

// uncomment these to solve performance problems with getRecentScrobbles
// 	$adodb->Execute("CREATE INDEX album_artistname_idx ON Album(artist_name)");
// 	$adodb->Execute("CREATE INDEX scrobbles_artist_idx ON Scrobbles(artist)");
//	$adodb->Execute("CREATE INDEX scrobbles_time_idx ON Scrobbles(time)");
//      $adodb->Execute("CREATE INDEX track_artist_idx ON Track(lower(artist_name))");
//      $adodb->Execute("CREATE INDEX track_name_idx ON Track(lower(name))");
//      $adodb->Execute("CREATE INDEX track_streamable_idx on Track(streamable);");
//      $adodb->Execute("CREATE INDEX scrobbles_artist_idx on Scrobbles(lower(artist))");
//      $adodb->Execute("CREATE INDEX scrobbles_track_idx on Scrobbles(lower(track))");
//      $adodb->Execute("CREATE UNIQE INDEX groups_groupname_idx ON Groups(lower(groupname))");

// uncomment these if you're using postgresql and want to run the software as www-data
//	$adodb->Execute("GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE Album, Artist, Auth, Clientcodes, Delete_Request Error, Invitation_Request, Invitations, Now_Playing, Places, Radio_Sessions, Scrobble_Sessions, Scrobbles, Scrobble_Track, Similar_Artist, Tags, Track, Users, User_Relationships, User_Relationship_Flags to \"www-data\"");
//	$adodb->Execute("GRANT SELECT ON Free_Scrobbles, Relationship_Flags to \"www-data\"");
//	$adodb->Execute("GRANT SELECT, UPDATE ON users_uniqueid_seq, scrobble_track_id_seq, groups_id_seq, artist_id_seq, album_id_seq to \"www-data\"");

	// Test user configuration
	$adodb->Execute("INSERT INTO Users
		(username, password, active)
		VALUES
		('testuser', '" . md5('password') . "', 1);");

	$adodb->Close();

	$submissions_server = $_POST['submissions'];
	$install_path = dirname(__FILE__) . "/";

	//Write out the configuration
	$config = "<?php\n \$config_version = " . $version .";\n \$connect_string = '" . $connect_string . "';\n \$submissions_server = '" . $submissions_server . "';\n \$install_path = '" . $install_path . "';\n \$adodb_connect_string = '" . $adodb_connect_string . "'; ";

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
			Submissions Server URL: <input type="text" name="submissions" value="<?php echo getAbsoluteURL(); ?>" /><br />
			<br />
			<input type="submit" value="Install" name="install" />
		</form>
	</body>
</html>


