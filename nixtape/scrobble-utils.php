<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2013 Free Software Foundation, Inc

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

/**
 * Functions used by TrackXML.php:scrobble() and TrackXML.php:updateNowPlaying()
 * Similar but not identical to gnukebox/scrobble-utils.php
 */

require_once('database.php');


/**
 * Add artist to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @return int				Artist ID.
 *
 * @todo Rename the function?
 */
function getOrCreateArtist($artist) {
	global $adodb;

	$query = 'SELECT id FROM Artist WHERE name=?';
	$params = array($artist);
	$artist_id = $adodb->GetOne($query, $params);

	if (!$artist_id) {
		// Artist doesn't exist, so we create them
		$query = 'INSERT INTO Artist (name) VALUES (?)';
		$params = array($artist);
		$res = $adodb->Execute($query, $params);
		return getOrCreateArtist($artist);
	} else {
		return $artist_id;
	}
}

/**
 * Add album to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @return int				Album ID.
 *
 *	@todo Rename the function?
 *	@todo Maybe we should return artist ID too.
 */
function getOrCreateAlbum($artist, $album) {
	global $adodb;

	$query = 'SELECT id FROM Album WHERE name=? AND artist_name=?';
	$params = array($album, $artist);
	$album_id = $adodb->GetOne($query, $params);

	if (!$album_id) {
		// Album doesn't exist, so create it

		// First check if artist exist, if not create it
		$artist_id = getOrCreateArtist($artist);

		$query = 'INSERT INTO Album (name, artist_name) VALUES (?,?)';
		$params = array($album, $artist);
		$adodb->Execute($query, $params);
		return getOrCreateAlbum($artist, $album);
	} else {
		return $album_id;
	}
}

/**
 * Add track to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @param string track		Track name.
 * @param string mbid		Track's musicbrainz ID.
 * @param int duration		Track length in seconds.
 * @return int				Track ID.
 *
 * @todo Rename the function?
 */
function getOrCreateTrack($artist, $album, $track, $mbid, $duration) {
	global $adodb;

	if ($album) {
		$query = 'SELECT id FROM Track WHERE name=? AND artist_name=? AND album_name=?';
		$params = array($track, $artist, $album);
	} else {
		$query = 'SELECT id FROM Track WHERE name=? AND artist_name=? AND album_name IS NULL';
		$params = array($track, $artist);
	}
	$track_id = $adodb->GetOne($query, $params);

	if (!$track_id) {
		// First check if artist and album exists, if not create them
		if ($album) {
			$album_id = getOrCreateAlbum($artist, $album);
		} else {
			$artist_id = getOrCreateArtist($artist);
		}
		
		// Create new track
		$query = 'INSERT INTO Track (name, artist_name, album_name, mbid, duration) VALUES (?,?,?,?,?)';
		$params = array($track, $artist, $album, $mbid, $duration);
		$adodb->Execute($query, $params);
		return getOrCreateTrack($artist, $album, $track, $mbid, $duration);
	} else {
		return $track_id;
	}
}

/**
 * Add track to Scrobble_Track db table
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @param string track		Track name.
 * @param string mbid		Track musicbrainz ID.
 * @param int duration		Track length in seconds.
 * @param int track_id		Track ID in Track database table
 * @return int				Scrobble_Track ID.
 */
function getOrCreateScrobbleTrack($artist, $album, $track, $mbid, $duration, $track_id) {
	global $adodb;

	$query = 'SELECT id FROM Scrobble_Track WHERE name=lower(?) AND artist=lower(?)';
	$params = array($track, $artist);

	if ($album) {
		$query .= ' AND album=lower(?)';
		$params[] = $album;
	} else {
		$query .= ' AND album IS NULL';
	}

	if ($mbid) {
		$query .= ' AND mbid=lower(?)';
		$params[] = $mbid;
	} else {
		$query .= ' AND mbid IS NULL';
	}

	$scrobbletrack_id = $adodb->GetOne($query, $params);

	if (!$scrobbletrack_id) {
		$query = 'INSERT INTO Scrobble_Track (name, artist, album, mbid, track) VALUES (lower(?), lower(?), lower(?), lower(?), ?)';
		$params = array($track, $artist, $album, $mbid, $track_id);
		$res = $adodb->Execute($query, $params);
		return getOrCreateScrobbleTrack($artist, $album, $track, $mbid, $duration, $track_id);
	} else {
		return $scrobbletrack_id;
	}
}

/**
 * Get scrobble session ID for a user.
 *
 * Gets the most recent scrobble session ID for userid,
 * or creates a new session ID if it can't find one.
 *
 * @param int userid (required)			User ID.
 * @param string api_key (optional)		Client API key (32 characters)
 * @return string						Session ID
 *
 * @todo Figure out how 2.0 clients can be identified (add api keys to clients array?)
 *		inprogress: added api_key field to Scrobble_Sessions
 * @todo We currently grab a sessionid that is not expired and has the right userid,
 *		this could have been created by another client and will display the wrong client id.
 *		inprogress: if api_key is specified, we also compare against api_key field in Scrobble_Sessions table.
 *
 * @todo rename the function?
 */
function getOrCreateScrobbleSession($userid, $api_key = null) {
	global $adodb;
	$query = 'SELECT sessionid FROM Scrobble_Sessions WHERE userid = ? AND expires > ?';
	$params = array($userid, time());

	if (strlen($api_key) == 32) {
		$query .= ' AND api_key=?';
		$params[] = $api_key;
	}

	$sessionid = $adodb->GetOne($query, $params);
	if (!$sessionid) {
		$sessionid = md5(mt_rand() . time());
		$expires = time() + 86400;
		$query = 'INSERT INTO Scrobble_Sessions(userid, sessionid, client, expires, api_key) VALUES (?,?,?,?,?)';
		$params = array($userid, $sessionid, $client_id, $expires, $api_key);
		try {
			$adodb->Execute($query, $params);
		} catch (Exception $e) {
			return null;
		}
	}
	return $sessionid;
}

/**
 * Correct artist/album/track/mbid/timestamp input
 *
 * @param mixed input Input to be corrected.
 * @param string type Type of input to be corrected.
 * @return array Array(mixed $old_input, mixed $corrected_input, int $corrected)
 *
 */
function correctInput($input, $type) {
	$old = $input;
	$new = $old;

	//TODO truncate strings at 255 chars or whatever the field limit is

	if ($type == 'artist' || $type == 'album' || $type == 'track') {
		$new = str_replace(' (PREVIEW: buy it at www.magnatune.com)', '', $new);
		$new = str_replace('testspam', '', $new);
		$new = trim($new);

		if (empty($new)) {
			$new = null;
		}
	} else if ($type == 'mbid') {
		if (isset($new)) {
			$new = strtolower(rtrim($new));
			if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $new)) {
				//do nothing
			} else {
				$new = null;
			}
		} else {
			$new = null;
		}
	} else if ($type == 'timestamp') {
		$new = (int) $new;
	} else if ($type == 'duration') {
		if($new) {
			$new = (int) $new;
		} else {
			$new = null;
		}
	}

	$result = array($old, $new, (int)($old != $new));
	return $result;
}

/**
 * Decide if and why we should ignore a track
 *
 * @param string artist Artist name
 * @param string track Track name
 * @param int timestamp Timestamp
 * @return array Array(int $ignored_code, string $ignored_message)
 *
 * @todo Rewrite code to look like the correctInput function?
 */
function ignoreInput($artist, $track, $timestamp) {
	$ignored_code = 0;
	$ignored_message = '';
	$timestamp_upperlimit = time() + 300;
	$timestamp_lowerlimit = 1009000000;

	if (empty($artist)) {
		$ignored_code = 1;
		$ignored_message = 'Artist was ignored';
	}
	if (empty($track)) {
		$ignored_code = 2;
		$ignored_message = 'Track was ignored';
	}
	if ($timestamp > $timestamp_upperlimit) {
		$ignored_message = 'Timestamp is too new';
		$ignored_code = 3;
	}
	if ($timestamp < $timestamp_lowerlimit) {
		$ignored_message = 'Timestamp is too old';
		$ignored_code = 4;
	}

	return array($ignored_code, $ignored_message);
}

/**
 * Tries to correct a track item's data or marks it as invalid.
 *
 * @param array item Array of data such as artist, album, track, duration..
 * @return array Same array as $item array, but with corrected data and added metadata.
 */
function validateScrobble($userid, $item) {
	// Correct scrobble data
	list($item['track_old'], $item['track'], $item['track_corrected']) = correctInput($item['track'], 'track');
	list($item['artist_old'], $item['artist'], $item['artist_corrected']) = correctInput($item['artist'], 'artist');
	list($item['album_old'], $item['album'], $item['album_corrected']) = correctInput($item['album'], 'album');
	list($item['mbid_old'], $item['mbid'], $item['mbid_corrected']) = correctInput($item['mbid'], 'mbid');
	list($item['duration_old'], $item['duration'], $item['duration_corrected']) = correctInput($item['duration'], 'duration');
	list($item['timestamp_old'], $item['timestamp'], $item['timestamp_corrected']) = correctInput($item['timestamp'], 'timestamp');
	$item['albumartist_corrected'] = 0; // we're currently not doing anything with albumartist in GNU FM

	// Validate scrobble, any $item with ignoredcode != 0 will not be scrobbled
	list($item['ignoredcode'], $item['ignoredmessage']) = ignoreInput($item['artist'], $item['track'], $item['timestamp']);
	if ($item['ignoredcode'] === 0) {
		$exists = scrobbleExists($userid, $item['artist'], $item['track'], $item['timestamp']);
		if ($exists) {
			$item['ignoredcode'] = 91; // GNU FM specific
			$item['ignoredmessage'] = 'Already scrobbled';
		}
	}

	return $item;
}
/**
 * Check if a scrobble has already been added to database.
 *
 * @param int userid		User ID
 * @param string artist		Artist name
 * @param string track		Track name
 * @param int time			Timestamp
 * @return bool				True is scrobble exists, False if not.
 */
function scrobbleExists($userid, $artist, $track, $time) {
	global $adodb;

	$query = 'SELECT time FROM Scrobbles WHERE userid=? AND artist=? AND track=? AND time=?';
	$params = array($userid, $artist, $track, $time);
	$res = $adodb->GetOne($query, $params);

	if (!$res) {
		return false;
	} else {
		return true;
	}
}
