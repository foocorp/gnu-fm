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
require_once($install_path . 'temp-utils.php');

if (!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t']) || !isset($_POST['i'])) {
	die("FAILED Required POST parameters are not set\n");
}
if (empty($_POST['s']) || empty($_POST['a']) || empty($_POST['t']) || empty($_POST['i'])) {
	die("FAILED Required POST parameters are empty\n");
}

if (!is_array($_POST['a']) || !is_array($_POST['t']) || !is_array($_POST['i'])) {
	die("FAILED Track parameters must be arrays\n");
}

$session_id = $_POST['s'];

$userid = useridFromSID($session_id);

$artist = $_POST['a'];
$track = $_POST['t'];
$timestamp = $_POST['i'];
//$source = $_POST['o'];
//$rating = $_POST['r'];
$duration = $_POST['l'];
$album = $_POST['b'];
$tracknumber = $_POST['n'];
$mbid = $_POST['m'];

// Convert timestamps to unix time if needed
for ($i = 0; $i < count($timestamp); $i++) {
	if (is_numeric($timestamp[$i])) {
		$timestamp[$i] = (int) $timestamp[$i];
	} else {
		// 1.1 time format
		date_default_timezone_set('UTC');
		$timestamp[$i] = strtotime($timestamp[$i]);
	}
}


$tracks_array = array();

if (is_array($artist)) {
	for ($i = 0; $i < count($artist); $i++) {
		$tracks_array[$i] = array(
			'artist' => $artist[$i],
			'track' => $track[$i],
			'timestamp' => $timestamp[$i],
			'album' => $album[$i],
			'tracknumber' => $tracknumber[$i],
			'mbid' => $mbid[$i],
			'albumartist' => $albumartist[$i],
			'duration' => $duration[$i],
		);
	}
} else {
	$tracks_array[0] = array(
		'artist' => $artist,
		'track' => $track,
		'timestamp' => $timestamp,
		'album' => $album,
		'tracknumber' => $tracknumber,
		'mbid' => $mbid,
		'albumartist' => $albumartist,
		'duration' => $duration,
	);
}


// Correct and inspect scrobbles to see if some should be ignored
for ($i = 0; $i < count($tracks_array); $i++) {
	$tracks_array[$i] = prepareTrack($userid, $tracks_array[$i], 'scrobble');
}

$adodb->StartTrans();
for ($i = 0; $i < count($tracks_array); $i++) {
	$t = $tracks_array[$i];
	if ($t['ignored_code'] === 0) {
		try {
			// Create artist, album and track if not already in db
			$t['track_id'] = getTrackID($t['artist'], $t['album'], $t['track'], $t['mbid'], $t['duration']);
			$t['scrobbletrack_id'] = getScrobbleTrackID($t['artist'], $t['album'], $t['track'], $t['mbid'], $t['duration'], $t['track_id']);
		} catch (Exception $e) {
			// Roll back database entries, log error and respond with error message
			$adodb->FailTrans();
			$adodb->CompleteTrans();
			reportError($e->getMessage(), $e->getTraceAsString());
			die('FAILED');
		}

		try {
			// Scrobble
			// TODO last.fm spec says we shouldnt scrobble corrected values,
			// so maybe we should only use corrected values for validation and in xml
			$query = 'INSERT INTO Scrobbles (userid, artist, album, track, time, mbid, source, rating, length, stid) VALUES (?,?,?,?,?,?,?,?,?,?)';
			$params = array(
				$userid,
				$t['artist'],
				$t['album'],
				$t['track'],
				$t['timestamp'],
				$t['mbid'],
				null,
				null,
				$t['duration'],
				$t['scrobbletrack_id']
			);
			$adodb->Execute($query, $params);
		} catch (Exception $e) {
			// Roll back database entries, log error and respond with error message
			$adodb->FailTrans();
			$adodb->CompleteTrans();
			reportError($e->getMessage(), $e->getTraceAsString());
			die('FAILED');
		}
	}
	$tracks_array[$i] = $t;
}
$adodb->CompleteTrans();

// Check if forwarding is enabled before looping through array
$params = array($userid);
$query = 'SELECT userid FROM Service_Connections WHERE userid = ? AND forward = 1';
$forward_enabled = $adodb->CacheGetOne(600, $query, $params);
if ($forward_enabled) {
	for ($i = 0; $i < count($tracks_array); $i++) {
		$t = $tracks_array[$i];
		if ($t['ignored_code'] === 0) {
			/* Forward scrobbles, we are forwarding unmodified input submitted by user,
			 * but only the scrobbles that passed our ignore filters, see prepareTrack(). */
			forwardScrobble($userid,
				$t['artist_old'],
				$t['album_old'],
				$t['track_old'],
				$t['timestamp_old'],
				$t['mbid_old'],
				null,
				null,
				$t['duration_old']);
		}
	}
}

die("OK\n");
