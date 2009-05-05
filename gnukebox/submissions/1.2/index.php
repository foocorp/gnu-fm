<?php
/* Libre.fm -- a free network service for sharing your music listening habits

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
$rowvalues = array();
$actualcount = 0;

for($i = 0; $i < count($_POST['a']); $i++) {
		switch (mb_detect_encoding($_POST['a'][$i])) {
		case "ASCII":
		case "UTF-8":
			$artist = $mdb2->quote($_POST['a'][$i], "text");
			break;
		default:
			die("FAILED Bad encoding in artist submission $i\n");
		}
	if(isset($_POST['b'][$i]) && !empty($_POST['b'])) {
		switch (mb_detect_encoding($_POST['b'][$i])) {
		case "ASCII":
		case "UTF-8":
			$album = $mdb2->quote($_POST['b'][$i], "text");
			break;
		default:
			die("FAILED Bad encoding in album submission $i\n");
		}
	} else {
		$album = 'NULL';
	}

	if (!isset($_POST['t'][$i]) || !isset($_POST['a'][$i]) || !isset($_POST['i'][$i])) {
	    $f = isset($_POST['t'][$i]) ? "T({$_POST['t'][$i]})" : 't';
	    $f .= isset($_POST['a'][$i]) ? "A({$_POST['a'][$i]})" : 'a';
	    $f .= isset($_POST['i'][$i]) ? "I({$_POST['i'][$i]})" : 'i';

	    die("FAILED Track $i was submitted with empty mandatory field(s): {$f}\n");
	}

	switch (mb_detect_encoding($_POST['t'][$i])) {
		case "ASCII":
		case "UTF-8":
		    $track = $mdb2->quote($_POST['t'][$i], "text");
		    break;
		default:
			die("FAILED Bad encoding in title submission $i\n");
	}
	if(is_numeric($_POST['i'][$i])) {
		$time = (int) $_POST['i'][$i];
	} else {
		// 1.1 time format
		date_default_timezone_set("UTC");
		$time = strtotime($_POST['i'][$i]);
	}

	$mb = validateMBID($_POST['m']);

	if($mb) {
		$mbid = $mdb2->quote($mb, "text");
	} else {
		$mbid = 'NULL';
	}

	if(isset($_POST['o'][$i])) {
		$source = $mdb2->quote($_POST['o'][$i], "text");
	} else {
		$source = 'NULL';
	}
	if(!empty($_POST['r'][$i])) {
		$rating = $mdb2->quote($_POST['r'][$i], "text");
	} else {
		$rating = $mdb2->quote("0", "text"); // use the fake rating code 0 for now
	}
	if(isset($_POST['l'][$i])) {
		$length = $mdb2->quote($_POST['l'][$i], "integer");
	} else {
		$length = 'NULL';
	}

	if(($time - time()) > 300) {
            die("FAILED Submitted track has timestamp in the future\n"); // let's try a 5-minute tolerance
	}

	createArtistIfNew($artist);
	if($album != 'NULL') {
		createAlbumIfNew($artist, $album);
	}
	$tid = getTrackCreateIfNew($artist, $album, $track, $mbid);
	$stid = getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid, $tid);

	$exists = scrobbleExists($username, $artist, $track, $time);

	if((!$exists) && $rating<>'S') {
	// Scrobble!
	$rowvalues[$actualcount] = "("
		. $username . ", "
		. $artist . ", "
		. $album . ", "
		. $track . ", "
		. $time . ", "
		. $mbid . ", "
		. $source . ","
		. $rating . ","
		. $length . ","
		. $stid . ")";

	$actualcount++;
	}

	if(($i+1) == count($_POST['a']) && $actualcount>0) {

		$mdb2->exec("BEGIN");

		for($j = 0; $j < $actualcount; $j++) {

	// Scrobble!
		$sql = "INSERT INTO Scrobbles (username, artist, album, track, time, mbid, source, rating, length, stid) VALUES " . $rowvalues[$j];
		$res =& $mdb2->exec($sql);
		if(PEAR::isError($res)) {
		    $msg = $res->getMessage() . " - " . $res->getUserInfo();
		    $mdb2->exec("ROLLBACK");
		    reportError($msg, $sql);
                    die("FAILED " . $msg . "\nError has been reported to site administrators.\n");
        	}

		}

		$mdb2->exec("COMMIT");

		if(PEAR::isError($res)) {
		    $msg = $res->getMessage() . " - " . $res->getUserInfo();
		    $mdb2->exec("ROLLBACK");
		    reportError($msg, $sql);
                    die("FAILED " . $msg . "\nError has been reported to site administrators.\n");
		}

	        // Destroy now_playing since it is almost certainly obsolescent
	        $mdb2->exec("DELETE FROM Now_Playing WHERE sessionid = " . $session_id);
	}
}

die("OK\n");

?>
