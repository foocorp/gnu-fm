<?
require_once('../../database.php');
require_once('../../scrobble-utils.php');

if(!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t']) || !isset($_POST['i'])) {
	die("FAILED Required POST parameters are not set");
}

if(!is_array($_POST['a']) || !is_array($_POST['t']) || !is_array($_POST['i'])) {
	die("FAILED Track parameters must be arrays");
}

$session_id = $_POST['s'];

$username = $mdb2->quote(usernameFromSID($session_id), "text");

for($i = 0; $i < count($_POST['a']); $i++) {
	$artist = $mdb2->quote($_POST['a'][$i], "text");
	if(isset($_POST['b'][$i])) {
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
	$mdb2->query("INSERT INTO Scrobbles (username, artist, track, time) VALUES ("
		. $username . ", "
		. $artist . ", "
		. $track . ", "
		. $time . ")");
}

die("OK");

?>
