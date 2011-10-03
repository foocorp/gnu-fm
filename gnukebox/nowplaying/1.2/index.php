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

require_once('../../database.php');
require_once('../../scrobble-utils.php');
require_once('../../auth-utils.php');

header('Content-Type: text/plain');

if (!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t'])) {
	die("FAILED Required POST parameters are not set\n");
}

//trim parameters
$session_id = trim($_POST['s']);
$artist = trim($_POST['a']);
$artist = noSpamTracks($artist);
$track = trim($_POST['t']);
$track = noSpamTracks($track);

if (empty($session_id) || empty($artist) || empty($track)) {
	die("FAILED Required POST parameters are empty\n");
}

if (isset($_POST['b'])) {
	$album = trim($_POST['b']);
	$album = noSpamTracks($album);
}
if (empty($album)) {
	$album = 'NULL';
}

if (isset($_POST['l']) && is_numeric($_POST['l'])) {
	$length = (int) $_POST['l'];
	if ($length > 5400) {
		$expires = time() + 600;
	} else {
		$expires = time() + (int) $_POST['l'];
	}
} else {
	$expires = time() + 250; //Expire in 5 minutes if we don't know the track length
}

$mbid = validateMBID($_POST['m']);
if (!$mbid) {
	$mbid = 'NULL';
}

//quote strings
$session_id = $adodb->qstr($session_id);
$artist = $adodb->qstr($artist);
$track = $adodb->qstr($track);
if($album != 'NULL') {
	$album = $adodb->qstr($album);
}
if ($mbid != 'NULL') {
	$mbid = $adodb->qstr($mbid);
}

//Delete this user's last playing song (if any)
$adodb->Execute('DELETE FROM Now_Playing WHERE sessionid = ' . ($session_id));

if (!check_session($session_id)) {
	die("BADSESSION\n");
}

try {
	$adodb->Execute('INSERT INTO Now_Playing (sessionid, artist, album, track, expires, mbid) VALUES ('
			. $session_id . ', '
			. $artist . ', '
			. $album . ', '
			. $track . ', '
			. $expires . ', '
			. $mbid . ')');
} catch (Exception $e) {
	die('FAILED ' . $e->getMessage() . "\n");
}

getTrackCreateIfNew($artist, $album, $track, $mbid);

//Expire old tracks
$adodb->Execute('DELETE FROM Now_Playing WHERE expires < ' . time());

die("OK\n");
