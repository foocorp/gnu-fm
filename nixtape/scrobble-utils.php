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
 * Get artist ID and add artist to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @return int				Artist ID.
 */
function getArtistID($artist) {
	global $adodb;

	$query = 'SELECT id FROM Artist WHERE name=?';
	$params = array($artist);
	$artist_id = $adodb->GetOne($query, $params);

	if (!$artist_id) {
		// Artist doesn't exist, so we create them
		$query = 'INSERT INTO Artist (name) VALUES (?)';
		$params = array($artist);
		$res = $adodb->Execute($query, $params);
		return getArtistID($artist);
	} else {
		return $artist_id;
	}
}

/**
 * Get album ID and add album to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @return int				Album ID.
 *
 *	@todo Maybe we should return artist ID too, we will need it when db gets normalized
 */
function getAlbumID($artist, $album) {
	global $adodb;

	$query = 'SELECT id FROM Album WHERE name=? AND artist_name=?';
	$params = array($album, $artist);
	$album_id = $adodb->GetOne($query, $params);

	if (!$album_id) {
		// Album doesn't exist, so create it

		// First check if artist exist, if not create it
		$artist_id = getArtistID($artist);

		$query = 'INSERT INTO Album (name, artist_name) VALUES (?,?)';
		$params = array($album, $artist);
		$adodb->Execute($query, $params);
		return getAlbumID($artist, $album);
	} else {
		return $album_id;
	}
}

/**
 * Get track ID and add track to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @param string track		Track name.
 * @param string mbid		Track's musicbrainz ID.
 * @param int duration		Track length in seconds.
 * @return int				Track ID.
 *
 */
function getTrackID($artist, $album, $track, $mbid, $duration) {
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
			$album_id = getAlbumID($artist, $album);
		} else {
			$artist_id = getArtistID($artist);
		}
		
		// Create new track
		$query = 'INSERT INTO Track (name, artist_name, album_name, mbid, duration) VALUES (?,?,?,?,?)';
		$params = array($track, $artist, $album, $mbid, $duration);
		$adodb->Execute($query, $params);
		return getTrackID($artist, $album, $track, $mbid, $duration);
	} else {
		return $track_id;
	}
}

/**
 * Get scrobble_track ID and add track to Scrobble_Track db table if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @param string track		Track name.
 * @param string mbid		Track musicbrainz ID.
 * @param int duration		Track length in seconds.
 * @param int track_id		Track ID in Track database table
 * @return int				Scrobble_Track ID.
 */
function getScrobbleTrackID($artist, $album, $track, $mbid, $duration, $track_id) {
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
		return getScrobbleTrackID($artist, $album, $track, $mbid, $duration, $track_id);
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
 * @return string						Scrobble session ID
 */
function getScrobbleSessionID($userid, $api_key = null) {
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

	if ($type == 'artist' || $type == 'album' || $type == 'track') {

		//Limit strings to 255 chars
		switch (mb_detect_encoding($new)) {
		case 'ASCII':
		case 'UTF-8':
			$new = mb_strcut($new, 0, 255, 'UTF-8');
			break;
		default:
			$new = null;
		}

		// Remove spam and trim whitespace
		$new = str_replace(' (PREVIEW: buy it at www.magnatune.com)', '', $new);
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
 * @param string input Input data
 * @param string type Type of input data
 * @return array Array(int $ignored_code, string $ignored_message)
 */
function ignoreInput($input, $type) {
	$ignored_code = 0;
	$ignored_message = '';

	if ($type == 'artist' && empty($input)) {
		$ignored_code = 1;
		$ignored_message = 'Artist was ignored';
	}

	if ($type == 'track' && empty($input)) {
		$ignored_code = 2;
		$ignored_message = 'Track was ignored';
	}

	if ($type == 'timestamp') {
		$timestamp_upperlimit = time() + 300;
		$timestamp_lowerlimit = 1009000000;
	
		if ($input > $timestamp_upperlimit) {
			$ignored_message = 'Timestamp is too new';
			$ignored_code = 3;
		}
		if ($input < $timestamp_lowerlimit) {
			$ignored_message = 'Timestamp is too old';
			$ignored_code = 4;
		}
	}

	return array($ignored_code, $ignored_message);
}

/**
 * Prepare a track for entering the database.
 * Tries to correct a track's data or marks it as invalid
 *
 * @param array t Array of track data.
 * @param int userid User ID.
 * @param string type Type of track, 'nowplaying' or 'scrobble'.
 * @return array Same array as t array, but with corrected data and added metadata.
 */
function prepareTrack($userid, $t, $type) {
	list($t['track_old'], $t['track'], $t['track_corrected']) = correctInput($t['track'], 'track');
	list($t['artist_old'], $t['artist'], $t['artist_corrected']) = correctInput($t['artist'], 'artist');
	list($t['album_old'], $t['album'], $t['album_corrected']) = correctInput($t['album'], 'album');
	list($t['mbid_old'], $t['mbid'], $t['mbid_corrected']) = correctInput($t['mbid'], 'mbid');
	list($t['duration_old'], $t['duration'], $t['duration_corrected']) = correctInput($t['duration'], 'duration');
	$t['albumartist_corrected'] = 0; // we're currently not doing anything with albumartist in GNU FM
	$t['tracknumber_corrected'] = 0; // we're currently not doing anything with tracknumber in GNU FM

	//TODO not pretty
	list($t['ignored_code'], $t['ignored_message']) = ignoreInput($t['artist'], 'artist');
	if($t['ignored_code'] === 0) {
		list($t['ignored_code'], $t['ignored_message']) = ignoreInput($t['track'], 'track');
	}

	if ($type == 'scrobble') {
		list($t['timestamp_old'], $t['timestamp'], $t['timestamp_corrected']) = correctInput($t['timestamp'], 'timestamp');

		if($t['ignored_code'] === 0) {
			list($t['ignoredcode'], $t['ignoredmessage']) = ignoreInput($t['timestamp'], 'timestamp');
		}
		if ($t['ignoredcode'] === 0) {
			$exists = scrobbleExists($userid, $t['artist'], $t['track'], $t['timestamp']);
			if ($exists) {
				$t['ignoredcode'] = 91; // GNU FM specific
				$t['ignoredmessage'] = 'Already scrobbled';
			}
		}
	}
	return $t;
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

/**
 * Sends a scrobble on to any other services the user has connected to their account
 *
 * @todo copied from gnukebox/scrobble-utils.php,
 *		we should review code and see if we can improve, additional params and batch scrobbling would be cool.
 * @todo docs
 */
function forwardScrobble($userid, $artist, $album, $track, $time, $mbid, $source, $rating, $length) {
	global $adodb, $lastfm_key, $lastfm_secret;

	$artist = rawurlencode($artist);
	$track = rawurlencode($track);
	$album = rawurlencode($album);
	$mbid = rawurlencode($mbid);
	$source = rawurlencode($source);
	$rating = rawurlencode($rating);
	$length = rawurlencode($length);

	$res = $adodb->CacheGetAll(600, 'SELECT * FROM Service_Connections WHERE userid = ' . $userid . ' AND forward = 1');
	foreach ($res as &$row) {
		$remote_key = $row['remote_key'];
		$ws_url = $row['webservice_url'];
		$curl_session = curl_init($ws_url);

		$post_vars = '';
		if ($album) {
			$post_vars .= 'album[0]=' . $album . '&';
		}
		$post_vars .= 'api_key=' . $lastfm_key . '&artist[0]=' . $artist;
		if ($length) {
			$post_vars .= '&length[0]=' . $length;
		}
		if ($mbid) {
			$post_vars .= '&mbid[0]=' . $mbid;
		}
		$post_vars .= '&method=track.scrobble';
		if ($rating) {
			$post_vars .= '&rating[0]=' . $rating;
		}
		$post_vars .= '&sk=' . $remote_key;
		if ($source) {
			$post_vars .= '&source[0]='. $source;
		}
		$post_vars .= '&timestamp[0]=' . $time . '&track[0]=' . $track;

		$sig = urldecode(str_replace('&', '', $post_vars));
		$sig = str_replace('=', '', $sig);
		$sig = md5($sig . $lastfm_secret);

		$post_vars .= '&api_sig=' . $sig;
		curl_setopt($curl_session, CURLOPT_POST, true);
		curl_setopt($curl_session, CURLOPT_POSTFIELDS, $post_vars);
		curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_session, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($curl_session, CURLOPT_TIMEOUT, 1);
		$response = curl_exec($curl_session);

		curl_close($curl_session);
	}
}
