<?
require_once('../../database.php');
require_once('../../scrobble-utils.php');

if(!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t'])) {
	die("FAILED Required POST parameters are not set\n");
}
if(empty($_POST['s']) || empty($_POST['a']) || empty($_POST['t'])) {
	die("FAILED Required POST parameters are empty\n");
}

$session_id = $_POST['s'];

$username = $mdb2->quote(usernameFromSID($session_id), "text");

$artist = $mdb2->quote($_POST['a'], "text");
if(isset($_POST['b'])) {
	$album = $mdb2->quote($_POST['b'], "text");
} else {
	$album = 'NULL';
}
$track = $mdb2->quote($_POST['t'], "text");
if(isset($_POST['l']) && is_numeric($_POST['l'])) {
	$expires = time() + (int) $_POST['l'];
} else {
	$expires = time() + 250; //Expire in 5 minutes if we don't know the track length
}

if(isset($_POST['m'])) {
	$mbid = $mdb2->quote($_POST['m'], "text");
} else {
	$mbid = 'NULL';
}

createArtistIfNew($artist);
if($album != 'NULL') {
	createAlbumIfNew($artist, $album);
}
createTrackIfNew($artist, $album, $track, $mbid);

//Expire old tracks
$mdb2->query("DELETE FROM Now_Playing WHERE expires < " . time());

//Delete this user's last playing song (if any)
$mdb2->query("DELETE FROM Now_Playing WHERE username = " . $username);

$mdb2->query("INSERT INTO Now_Playing (username, artist, track, expires, mbid) VALUES ("
	. $username . ", "
	. $artist . ", "
	. $track . ", "
	. $expires . ", "
	. $mbid . ")");


die("OK\n");

?>
