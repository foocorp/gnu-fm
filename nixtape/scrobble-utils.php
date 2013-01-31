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

/**
 * Add track to database if it doesnt already exist.
 *
 * @param string artist		Artist name.
 * @param string album		Album name.
 * @param string track		Track name.
 * @param string mbid		Track's musicbrainz ID.
 * @return int				Album ID.
 *
 * @todo Possible problem when doing case insensitive search for artist/album/track
 *	if foreign key checks are case sensitive when scrobbling with
 *	different casing than the one inserted below.
 *	@todo Rename the function?
 */
function getTrackCreateIfNew($artist, $album, $track, $mbid) {
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
		$query = 'INSERT INTO Track (name, artist_name, album_name, mbid) VALUES (?,?,?,?)';
		$params = array($track, $artist, $album, $mbid);
		$adodb->Execute($query, $params);
		return getTrackCreateIfNew($artist, $album, $track, $mbid);
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
