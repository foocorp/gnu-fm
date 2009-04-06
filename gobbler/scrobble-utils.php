<?
require_once('database.php');

function usernameFromSID($session_id) {
	global $mdb2;

	// Delete any expired session ids
	$mdb2->query("DELETE FROM Scrobble_Sessions WHERE expires < " . time());

	$res = $mdb2->query("SELECT username FROM Scrobble_Sessions WHERE sessionid = " . $mdb2->quote($session_id, "text"));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		die("BADSESSION");
	}

	return $res->fetchOne(0);
}

function createArtistIfNew($artist) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Artist WHERE name = " . utf8html($artist));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Artist doesn't exist, so we create them
		$res = $mdb2->query("INSERT INTO Artist (name) VALUES (" . utf8html($artist) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function createAlbumIfNew($artist, $album) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Album WHERE name = " . utf8html($album));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Album doesn't exist, so create it
		$res = $mdb2->query("INSERT INTO Album (name, artist_name) VALUES (" . utf8html($album) . ", " . utf8html($artist) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function createTrackIfNew($artist, $album, $track, $mbid) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Track WHERE name = " . utf8html($track) . " AND artist = " . utf8html($artist));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Create new track
		$res = $mdb2->query("INSERT INTO Track (name, artist, album, mbid) VALUES ("
			. utf8html($track) . ", "
			. utf8html($artist) . ", "
			. utf8html($album) . ", "
			. utf8html($mbid) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function utf8html(&$input) {
    $input = utf8_encode(htmlentities($input));
}
?>
