<?
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

require_once('../../database.php');
require_once('../../scrobble-utils.php');

if(!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t']) || !isset($_POST['i'])) {
	die("Failed Required POST parameters are not set\n");
}
if(empty($_POST['s']) || empty($_POST['a']) || empty($_POST['t']) || empty($_POST['i'])) {
	die("Failed Required POST parameters are empty\n");
}

if(!is_array($_POST['a']) || !is_array($_POST['t']) || !is_array($_POST['i'])) {
	die("FAILED Track parameters must be arrays\n");
}

$session_id = $_POST['s'];

$username = $mdb2->quote(usernameFromSID($session_id), "text");

for($i = 0; $i < count($_POST['a']); $i++) {
	$artist = $mdb2->quote($_POST['a'][$i], "text");
	if(isset($_POST['b'][$i]) && !empty($_POST['b'])) {
		$album = $mdb2->quote($_POST['b'][$i], "text");
	} else {
		$album = 'NULL';
	}
	$track = $mdb2->quote($_POST['t'][$i], "text");
	if(is_numeric($_POST['i'][$i])) {
		$time = (int) $_POST['i'][$i];
	} else {
		// 1.1 time format
		$time = strtotime($_POST['i'][$i]);
	}
	if(isset($_POST['m'][$i])) {
		$mbid = $mdb2->quote($_POST['m'][$i], "text");
	} else {
		$mbid = 'NULL';
	}

	createArtistIfNew($artist);
	if($album != 'NULL') {
		createAlbumIfNew($artist, $album);
	}
	createTrackIfNew($artist, $album, $track, $mbid);

	// Scrobble!
	$mdb2->query("INSERT INTO Scrobbles (username, artist, track, time, mbid) VALUES ("
		. $username . ", "
		. $artist . ", "
		. $track . ", "
		. $time . ", "
		. $mbid . ")");

        // Destroy now_playing since it is almost certainly obsolescent
        $mdb2->query("DELETE FROM Now_Playing WHERE username = " . $username);
}

die("OK\n");

?>
