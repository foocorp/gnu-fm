<?
require_once('../../database.php');
require_once('../../scrobble-utils.php');

if(!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t']) || !isset($_POST['i']) || !isset($_POST['o'])) {
	die("FAILED Required POST parameters are not set");
}

if(!is_array($_POST['a']) || !is_array($_POST['t']) || !is_array($_POST['i']) || !is_array($_POST['o'])) {
	die("FAILED Track parameters must be arrays");
}

$session_id = $_POST['s'];

// Delete any expired session ids
$mdb2->query("DELETE FROM Scrobble_Sessions WHERE expires < " . time());

$res = $mdb2->query("SELECT username FROM Scrobble_Sessions WHERE sessionid = " . $mdb2->quote($session_id, "text"));
if(PEAR::isError($res)) {
	die("FAILED " . $res->getMessage());
}

if(!$res->numRows()) {
	die("BADSESSION");
}

$username = $mdb2->quote($res->fetchOne(0), "text");

for($i = 0; $i < count($_POST['a']); $i++) {
	$artist = $mdb2->quote($_POST['a'][$i], "text");
	if(isset($_POST['b'][$i])) {
		$album = $mdb2->quote($_POST['b'][$i], "text");
	} else {
		$album = 'NULL';
	}
	$track = $mdb2->quote($_POST['t'][$i], "text");
	$time = (int) $_POST['i'][$i];
	$origin = $mdb2->quote($_POST['o'][$i], "text");
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
	$res = $mdb2->query("SELECT playcount FROM Scrobbles WHERE username = " . $username . " AND track = " . $track . " AND artist = " . $artist);
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// New scrobble entry
		$mdb2->query("INSERT INTO Scrobbles (username, artist, track, lastplayed, playcount) VALUES ("
			. $username . ", "
			. $artist . ", "
			. $track . ", "
			. $time . ", "
			. "1)");
	} else {
		$playcount = $res->fetchOne(0);
		$playcount++;
		$mdb2->query("UPDATE Scrobbles SET "
			. "playcount = " . $playcount . ", "
			. "lastplayed = " . $time
			. " WHERE username = " . $username 
			. " AND artist = " . $artist 
			. " AND track = " . $track);
	}
}

die("OK");

?>
