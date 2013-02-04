<?php
/* GNU FM -- a free network service for sharing your music listening habits

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

require_once($install_path . '/database.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . '/scrobble-utils.php');
require_once('xml.php');

/**
 * Class with functions that returns XML-formatted data for tracks.
 *
 * These functions are mainly used by web service methods.
 *
 * @package API
 */
class TrackXML {

	public static function addTags($userid, $artist, $album, $trackName, $tags) {
		try {
			$track = new Track($trackName, $artist);
			$track->addTags($tags, $userid);
		} catch (Exception $e) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		return $xml;
	}

	public static function getTopTags($artist, $name, $limit, $cache) {

		try {
			$track = new Track($name, $artist);
			$res = $track->getTopTags($limit, 0, $cache);
		} catch (Exception $e) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		if(!$res) {
			return(XML::error('failed', '6', 'No tags for this track'));
		}
	
		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptags', null);
		$root->addAttribute('artist', $artist);
		$root->addAttribute('track', $name);

		foreach ($res as &$row) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($row['tag']));
			$tag_node->addChild('count', $row['freq']);
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}

	public static function getTags($artist, $name, $userid, $limit, $cache) {
		
		try {
			$track = new Track($name, $artist);
			$res = $track->getTags($userid, $limit, 0, $cache);
		} catch (Exception $e) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		if(!$res) {
			return(XML::error('failed', '6', 'No tags for this track'));
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		$root = $xml->addChild('tags', null);
		$root->addAttribute('artist', $artist);
		$root->addAttribute('track', $name);

		foreach ($res as &$row) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($row['tag']));
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}

	public static function ban($artist, $name, $userid) {
		global $adodb;

		try {
			$res = $adodb->Execute('INSERT INTO Banned_Tracks VALUES ('
				. $userid . ', '
				. $adodb->qstr($name) . ', '
				. $adodb->qstr($artist) . ', '
				. time() . ')');
		} catch (Exception $e) {}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		return $xml;
	}

	public static function love($artist, $name, $userid) {
		global $adodb;

		try {
			$res = $adodb->Execute('INSERT INTO Loved_Tracks VALUES ('
				. $userid . ', '
				. $adodb->qstr($name) . ', '
				. $adodb->qstr($artist) . ', '
				. time() . ')');
		} catch (Exception $e) {}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		return $xml;
	}

	public static function unban($artist, $name, $userid) {
		global $adodb;

		try {
			$res = $adodb->Execute('DELETE FROM Banned_Tracks WHERE userid=' . $userid . ' AND track=' . $adodb->qstr($name) . ' AND artist=' . $adodb->qstr($artist));
		} catch (Exception $e) {}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		return $xml;
	}


	public static function unlove($artist, $name, $userid) {
		global $adodb;

		try {
			$res = $adodb->Execute('DELETE FROM Loved_Tracks WHERE userid='	. $userid . ' AND track=' . $adodb->qstr($name) . ' AND artist=' . $adodb->qstr($artist));
		} catch (Exception $e) {}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		return $xml;
	}


	public static function updateNowPlaying($userid, $artist, $track, $album, $trackNumber, $context, $mbid, $duration, $albumArtist, $api_key) {
		global $adodb;

		list($artist_old, $artist, $artist_corrected) = correctInput($artist, 'artist');
		list($track_old, $track, $track_corrected) = correctInput($track,  'track');
		list($album_old, $album, $album_corrected) = correctInput($album, 'album');
		list($mbid_old, $mbid, $mbid_corrected) = correctInput($mbid, 'mbid');
		list($duration_old, $duration, $duration_corrected) = correctInput($duration, 'duration');

		list($ignored_code, $ignored_message) = ignoreInput($artist, $track, time()); //TODO remove ugly time hack

		// Get a scrobble session id. TODO check if we got one
		$sessionid = getOrCreateScrobbleSession($userid, $api_key);

		// Delete last played track
		$query = 'DELETE FROM Now_Playing WHERE sessionid = ?';
		$params = array($sessionid);
		try {
			$adodb->Execute($query, $params);
		} catch (Exception $e) {}

		//calculate expiry time
		if (!$duration || ($duration > 5400)) {
			// Default expiry time of 5 minutes if $duration is false or above 5400
			$expires = time() + 300;
		} else {
			$expires = time() + $duration;
		}

		if (!$ignored_code) {
			//TODO Clean up expired tracks in now_playing table

			// Create artist, album, track if not in db
			try {
				getOrCreateTrack($artist, $album, $track, $mbid, $duration);

				// Add new track to database
				$query = 'INSERT INTO Now_Playing(sessionid, track, artist, album, mbid, expires) VALUES (?,?,?,?,?,?)';
				$params = array($sessionid, $track, $artist, $album, $mbid, $expires);
				$adodb->Execute($query, $params);

			} catch (Exception $e) {
				//TODO roll back
				reportError($e->getMessage(), $e->getTraceAsString());
				return XML::error('failed', '16', 'The service is temporarily unavailable, please try again.');
			}
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('nowplaying', null);
		$track_node = $root->addChild('track', repamp($track));
		$track_node->addAttribute('corrected', $track_corrected);
		$artist_node = $root->addChild('artist', repamp($artist));
		$artist_node->addAttribute('corrected', $artist_corrected);
		$album_node = $root->addChild('album', repamp($album));
		$album_node->addAttribute('corrected', $album_corrected);
		$albumartist_node = $root->addChild('albumArtist', null); //TODO
		$albumartist_node->addAttribute('corrected', '0'); //TODO
		$ignoredmessage_node = $root->addChild('ignoredMessage', $ignored_message);
		$ignoredmessage_node->addAttribute('code', $ignored_code);
		/* begin debug
		 */
		$debug = $root->addChild('debug', null);
		$duration_node = $debug->addChild('duration', $duration);
		$duration_node->addAttribute('corrected', $duration_corrected);
		$expires_node = $debug->addChild('expires_in', $expires - time());
		/* end debug */

		return $xml;
	}


	public static function scrobble($userid, $artist, $track, $timestamp, $album, $tracknumber, $mbid, $albumartist, $duration, $api_key) {
		global $adodb;
		// Get a scrobble session id. TODO check if we got one
		$sessionid = getOrCreateScrobbleSession($userid, $api_key);

		$accepted_count = 0;
		$ignored_count = 0;
		$tracks_array = array();


		// convert input to trackitem arrays and add them to tracks_array
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
			$item = $tracks_array[$i];
			$item_corrected = validateScrobble($userid, $item);
			$tracks_array[$i] = $item_corrected;
		}
		

		// if valid, create any artist, album and track not already in db, then scrobble
		//$adodb->StartTrans();
		for ($i = 0; $i < count($tracks_array); $i++) {
			$item = $tracks_array[$i];
			if ($item['ignoredcode'] === 0) {
				try {
					// First check if track exists, if not create it
					$track_id = getOrCreateTrack($item['artist'], $item['album'], $item['track'], $item['mbid'], $item['duration']);

					$item['scrobbletrack_id'] = getOrCreateScrobbleTrack($item['artist'], $item['album'], $item['track'], $item['mbid'], $item['duration'], $track_id);
				} catch (Exception $e) {
					//rollback and display respond with error message
					//$adodb->FailTrans();
					//$adodb->CompleteTrans();
					reportError($e->getMessage(), $e->getTraceAsString());
					//return XML::error('failed', '16', 'The service is temporarily unavailable, please try again.');

					//TODO uncomment StartTrans, FailTrans, CompleteTrans, return XML when removing debug block below
					/*begin debug*/
					$item['ignoredcode'] = 92;
					$item['ignoredmessage'] = $e;
					/*end debug*/
				}
			}

			// if still valid track, scrobble it
			if ($item['ignoredcode'] === 0) { //TODO we can remove this if block when debug code is removed
				try {
					//scrobble
					// TODO last.fm spec says we shouldnt scrobble corrected values,
					// so maybe we should only use corrected values for validation and in xml
					$query = 'INSERT INTO Scrobbles (serid, artist, album, track, time, mbid, source, rating, length, stid) VALUES (?,?,?,?,?,?,?,?,?,?)';
					$params = array(
						$userid,
						$item['artist'],
						$item['album'],
						$item['track'],
						$item['timestamp'],
						$item['mbid'],
						null,
						null,
						$item['duration'],
						$item['scrobbletrack_id']
					);
					$adodb->Execute($query, $params);
				} catch (Exception $e) {
					//$adodb->FailTrans();  //rollback all scrobbles if any inserts fail
					//$adodb->CompleteTrans();
					reportError($e->getMessage(), $e->getTraceAsString());
					//return XML::error('failed', '16', 'The service is temporarily unavailable, please try again.');
					/*begin debug
					 */
					$item['ignoredcode'] = 93;
					$item['ignoredmessage'] = $e;
					/*end debug*/
				}
			}
			$tracks_array[$i] = $item;
		}
		//$adodb->CompleteTrans();


		/*TODO forward any valid scrobbles here?
		 * If we want to forward untouched track data, we should use the $item[*_old] data.
		 */


		//build xml
		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('scrobbles', null);

		for ($i = 0; $i < count($tracks_array); $i++) {
			$item = $tracks_array[$i];

			$scrobble = $root->addChild('scrobble', null);
			$track_node = $scrobble->addChild('track', repamp($item['track']));
			$track_node->addAttribute('corrected', $item['track_corrected']);
			$artist_node = $scrobble->addChild('artist', repamp($item['artist']));
			$artist_node->addAttribute('corrected', $item['artist_corrected']);
			$album_node = $scrobble->addChild('album', repamp($item['album']));
			$album_node->addAttribute('corrected', $item['album_corrected']);
			$albumartist_node = $scrobble->addChild('albumArtist', repamp($item['albumartist']));
			$albumartist_node->addAttribute('corrected', $item['albumartist_corrected']);
			$scrobble->addChild('timestamp', $item['timestamp']);
			$ignoredmessage_node = $scrobble->addChild('ignoredMessage', $item['ignoredmessage']);
			$ignoredmessage_node->addAttribute('code', $item['ignoredcode']);
			/** begin debug
			 */
			$debug = $scrobble->addChild('debug', null);
			$debug->addChild('tracknumber', $item['tracknumber']);
			$mbid_node = $debug->addChild('mbid', $item['mbid']);
			$mbid_node->addAttribute('corrected', $item['mbid_corrected']);
			$duration_node = $debug->addChild('duration', $item['duration']);
			$duration_node->addAttribute('corrected', $item['duration_corrected']);
			/* end debug*/

			if ($item['ignoredcode'] === 0) {
				$accepted_count += 1;
			} else {
				$ignored_count += 1;
			}

		}
		$root->addAttribute('accepted', $accepted_count);
		$root->addAttribute('ignored', $ignored_count);

		return $xml;
	}

}
