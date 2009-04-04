<?
require_once('database.php');

function createArtistIfNew($artist) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Artist WHERE name = " . $artist);
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Artist doesn't exist, so we create them
		$res = $mdb2->query("INSERT INTO Artist (name) VALUES (" . $artist . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function createAlbumIfNew($artist, $album) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Album WHERE name = " . $album);
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Album doesn't exist, so create it
		$res = $mdb2->query("INSERT INTO Album (name, artist_name) VALUES (" . $album . ", " . $artist . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function createTrackIfNew($artist, $album, $track, $mbid) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Track WHERE name = " . $track . " AND artist = " . $artist);
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Create new track
		$res = $mdb2->query("INSERT INTO Track (name, artist, album, mbid) VALUES ("
			. $track . ", "
			. $artist . ", "
			. $album . ", "
			. $mbid . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

?>
