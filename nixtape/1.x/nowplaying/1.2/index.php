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

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($install_path . 'database.php');
require_once($install_path . 'scrobble-utils.php');
require_once($install_path . '1.x/auth-utils.php');

header('Content-Type: text/plain');

if (!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t'])) {
	die("FAILED Required POST parameters are not set\n");
}

$sessionid = trim($_POST['s']);
if (!check_session($sessionid)) {
	die("BADSESSION\n");
}

$t = array(
	'artist' => $_POST['a'],
	'track' => $_POST['t'],
	'album' => $_POST['b'],
	'tracknumber' => $_POST['n'],
	'mbid' => $_POST['m'],
	'duration' => $_POST['l'],
	'albumartist' => $albumartist
);

$t = prepareTrack($userid, $t, 'nowplaying');

// Delete last played track
$query = 'DELETE FROM Now_Playing WHERE sessionid = ?';
$params = array($sessionid);
try {
	$adodb->Execute($query, $params);
} catch (Exception $e) {}

// Calculate expiry time
if (!$t['duration'] || ($t['duration'] > 5400)) {
	// Default expiry time of 300 seconds if duration is false or above 5400 seconds
	$expires = time() + 300;
} else {
	$expires = time() + $t['duration'];
}

if ($t['ignored_code'] === 0) {
	// Clean up expired tracks in now_playing table
	$params = array(time());
	$query = 'DELETE FROM Now_Playing WHERE expires < ?';
	$adodb->Execute($query, $params);
		$adodb->StartTrans();
	try {
		// getTrackID will create the track in Track table if it doesnt exist
		getTrackID($t['artist'], $t['album'], $t['track'], $t['mbid'], $t['duration']);
			$params = array($sessionid, $t['track'], $t['artist'], $t['album'], $t['mbid'], $expires);
		$query = 'INSERT INTO Now_Playing(sessionid, track, artist, album, mbid, expires) VALUES (?,?,?,?,?,?)';
		$adodb->Execute($query, $params);
		} catch (Exception $e) {
		$adodb->FailTrans();
		$adodb->CompleteTrans();
		reportError($e->getMessage(), $e->getTraceAsString());
		die('FAILED');
	}
	$adodb->CompleteTrans();
}

die("OK\n");
