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

require_once('database.php');

//TODO Docs
function createArtistIfNew($artist) {
	// TODO Possible problem when doing case insensitive search for artist,
	// if foreign key checks are case sensitive when scrobbling an artist with
	// different casing from the one inserted below.

	global $adodb;

	$query = 'SELECT id FROM Artist WHERE lower(name) = lower(?)';
	$params = array($artist);
	$id = $adodb->GetOne($query, $params);

	if (!$id) {
		// Artist doesn't exist, so we create them
		$query = 'INSERT INTO Artist (name) VALUES (?)';
		$params = array($artist);
		$res = $adodb->Execute($query, $params);
	}
}

//TODO Docs
function createAlbumIfNew($artist, $album) {
	global $adodb;

	$query = 'SELECT  id FROM Album WHERE lower(name) = lower(?) AND lower(artist_name) = lower(?)';
	$params = array($album, $artist);
	$id = $adodb->GetOne($query, $params);

	if (!$id) {
		// Album doesn't exist, so create it

		// First check if artist exist, if not create it
		createArtistIfNew($artist);

		$query = 'INSERT INTO Album (name, artist_name) VALUES (?,?)';
		$params = array($album, $artist);
		$adodb->Execute($query, $params);
	}
}

//TODO Docs
function getTrackCreateIfNew($artist, $album, $track, $mbid) {
	global $adodb;

	if ($album) {
		$query = 'SELECT id FROM Track WHERE lower(name) = lower(?) AND lower(artist_name) = lower(?) AND lower(album_name) = lower(?)';
		$params = array($track, $artist, $album);
	} else {
		$query = 'SELECT id FROM Track WHERE lower(name) = lower(?) AND lower(artist_name) = lower(?) AND album_name IS NULL';
		$params = array($track, $artist);
	}
	$res = $adodb->GetOne($query, $params);

	if (!$res) {
		// First check if artist and album exists, if not create them
		if ($album) {
			createAlbumIfNew($artist, $album);
		} else {
			createArtistIfNew($artist);
		}
		
		// Create new track
		$query = 'INSERT INTO Track (name, artist_name, album_name, mbid) VALUES (?,?,?,?)';
		$params = array($track, $artist, $album, $mbid);
		$res = $adodb->Execute($query, $params);
		return getTrackCreateIfNew($artist, $album, $track, $mbid);
	} else {
		return $res;
	}
}

/**
 * Get or create a scrobble session for a user.
 *
 * @param int userid (required)			User ID.
 * @param string clientid (optional)	Client ID (3 letters) //TODO Not sure if we even need this.
 * @return string						Session ID
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
