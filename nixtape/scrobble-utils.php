<?php

/* GNUkebox -- a free software server for recording your listening habits

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
 * @todo Possible problem when doing case insensitive search for artist
 *	if foreign key checks are case sensitive when scrobbling with
 *	different casing than the one inserted below.
 *	@todo Rename the function?
 */
function createArtistIfNew($artist) {
	global $adodb;

	$query = 'SELECT id FROM Artist WHERE lower(name) = lower(?)';
	$params = array($artist);
	$artist_id = $adodb->GetOne($query, $params);

	if (!$artist_id) {
		// Artist doesn't exist, so we create them
		$query = 'INSERT INTO Artist (name) VALUES (?)';
		$params = array($artist);
		$res = $adodb->Execute($query, $params);
		return createArtistIfNew($artist);
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
 * @todo Possible problem when doing case insensitive search for artist/album
 *	if foreign key checks are case sensitive when scrobbling with
 *	different casing than the one inserted below.
 *	@todo Rename the function?
 *	@todo Maybe we should return artist ID too.
 */
function createAlbumIfNew($artist, $album) {
	global $adodb;

	$query = 'SELECT  id FROM Album WHERE lower(name) = lower(?) AND lower(artist_name) = lower(?)';
	$params = array($album, $artist);
	$album_id = $adodb->GetOne($query, $params);

	if (!$album_id) {
		// Album doesn't exist, so create it

		// First check if artist exist, if not create it
		$artist_id = createArtistIfNew($artist);

		$query = 'INSERT INTO Album (name, artist_name) VALUES (?,?)';
		$params = array($album, $artist);
		$adodb->Execute($query, $params);
		return createAlbumIfNew($artist, $album);
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
 * @return int				Album ID.
 *
 * @todo Possible problem when doing case insensitive search for artist/album/track
 *	if foreign key checks are case sensitive when scrobbling with
 *	different casing than the one inserted below.
 *	@todo Rename the function?
 */
function getTrackCreateIfNew($artist, $album, $track, $mbid, $duration) {
	global $adodb;

	if ($album) {
		$query = 'SELECT id FROM Track WHERE lower(name) = lower(?) AND lower(artist_name) = lower(?) AND lower(album_name) = lower(?)';
		$params = array($track, $artist, $album);
	} else {
		$query = 'SELECT id FROM Track WHERE lower(name) = lower(?) AND lower(artist_name) = lower(?) AND album_name IS NULL';
		$params = array($track, $artist);
	}
	$track_id = $adodb->GetOne($query, $params);

	if (!$track_id) {
		// First check if artist and album exists, if not create them
		if ($album) {
			$album_id = createAlbumIfNew($artist, $album);
		} else {
			$artist_id = createArtistIfNew($artist);
		}
		
		// Create new track
		$query = 'INSERT INTO Track (name, artist_name, album_name, mbid, duration) VALUES (?,?,?,?,?)';
		$params = array($track, $artist, $album, $mbid, $duration);
		$adodb->Execute($query, $params);
		return getTrackCreateIfNew($artist, $album, $track, $mbid, $duration);
	} else {
		return $track_id;
	}
}

/**
 * Get scrobble session ID for a user.
 *
 * Gets the most recent scrobble session ID for userid,
 * or creates a new session ID if it can't find one.
 *
 * @param int userid (required)			User ID.
 * @param string clientid (optional)	Client ID (max 3 characters)
 * @return string						Session ID
 *
 * @todo Figure out how 2.0 client can be identified
 * @todo rename the function?
 */
function getOrCreateScrobbleSession($userid, $clientid=null) {
	global $adodb;
	$query = 'SELECT sessionid FROM Scrobble_Sessions WHERE userid = ? AND expires > ? ORDER BY expires DESC';
	$params = array($userid, time());
	$sessionid = $adodb->GetOne($query, $params);
	if (!$sessionid) {
		$sessionid = md5(mt_rand() . time());
		$expires = time() + 86400;
		$query = 'INSERT INTO Scrobble_Sessions(userid, sessionid, client, expires) VALUES (?,?,?,?)';
		$params = array($userid, $sessionid, $clientid, $expires);
		$adodb->Execute($query, $params);
	}
	return $sessionid;
}

/**
 * Correct artist/album/track/mbid/timestamp input
 *
 * Returns array with $corrected_input with corrected input,
 * and string $corrected with '1' or '0' depending on if the input was corrected.
 *
 * @param mixed input Input to be corrected.
 * @param string type Type of input to be corrected.
 * @return array Array(mixed $corrected_input, string $corrected)
 *
 * @todo docs
 */
function correctInput($input, $type) {
	$old = $input;
	$new = $old;

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
		//TODO do stuff
	} else if ($type == 'duration') {
		$new = (int) $new;
	}

	if ($old === $new) {
		$corrected = '0';
	} else {
		$corrected = '1';
	}
	$result = array($new, $corrected);
	return $result;
}

/**
 * Decide if and why we should ignore a track
 *
 * @param string artist Artist name
 * @param string track Track name
 * @param int timestamp Timestamp
 * @return array Array(int $ignored_code, string $ignored_message)
 */
function ignoreInput($artist, $track, $timestamp) {
	$ignored_code = 0;
	$ignored_message = '';

	//TODO calculate timestamp $upperlimit, $lowerlimit

	if (empty($artist)) {
		$ignored_code = 1;
		$ignored_message = 'Artist was ignored';
	}
	if (empty($track)) {
		$ignored_code = 2;
		$ignored_message = 'Track was ignored';
	}
	if ($timestamp > $upperlimit) {
		$ignored_message = 'Timestamp is too new';
		$ignored_code = '3';
	}
	if ($timestamp < $lowerlimit) {
		$ignored_message = 'Timestamp is too old';
		$ignored_code = '4';
	}

	return array($ignored_code, $ignored_message);
}
