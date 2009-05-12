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

if(!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t'])) {
	die("FAILED Required POST parameters are not set\n");
}
if(empty($_POST['s']) || empty($_POST['a']) || empty($_POST['t'])) {
	die("FAILED Required POST parameters are empty\n");
}

$session_id = $_POST['s'];

$username = $adodb->qstr(usernameFromSID($session_id));
$MQsess = $adodb->qstr($session_id);

$artist = $adodb->qstr($_POST['a']);
if(isset($_POST['b'])) {
	$album = $adodb->qstr($_POST['b']);
} else {
	$album = 'NULL';
}
$track = $adodb->qstr($_POST['t']);
if(isset($_POST['l']) && is_numeric($_POST['l'])) {
	$expires = time() + (int) $_POST['l'];
} else {
	$expires = time() + 250; //Expire in 5 minutes if we don't know the track length
}

$mb = validateMBID($_POST['m']);

if($mb) {
	$mbid = $adodb->qstr($mb);
} else {
	$mbid = 'NULL';
}

createArtistIfNew($artist);
if($album != 'NULL') {
	createAlbumIfNew($artist, $album);
}
getTrackCreateIfNew($artist, $album, $track, $mbid);

//Expire old tracks
$adodb->Execute("DELETE FROM Now_Playing WHERE expires < " . time());

//Delete this user's last playing song (if any)
$adodb->Execute("DELETE FROM Now_Playing WHERE sessionid = " . ($MQsess));

try {
$adodb->Execute("INSERT INTO Now_Playing (sessionid, artist, album, track, expires, mbid) VALUES ("
	. $MQsess . ", "
	. $artist . ", "
	. $album . ", "
	. $track . ", "
	. $expires . ", "
	. $mbid . ")");
}
catch (exception $e) {
	die("FAILED " . $e->getMessage() . "\n");
}

die("OK\n");

?>
