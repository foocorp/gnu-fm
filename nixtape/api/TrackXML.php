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


	public static function updateNowPlaying($userid, $artist, $track, $album, $tracknumber, $context, $mbid, $duration, $albumartist, $api_key) {
		global $adodb;

		$sessionid = getScrobbleSessionID($userid, $api_key);

		$t = array(
			'artist' => $artist,
			'track' => $track,
			'album' => $album,
			'tracknumber' => $tracknumber,
			'mbid' => $mbid,
			'duration' => $duration,
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
				return XML::error('failed', '16', 'The service is temporarily unavailable, please try again.');
			}
			$adodb->CompleteTrans();
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('nowplaying', null);
		$track_node = $root->addChild('track', repamp($t['track']));
		$track_node->addAttribute('corrected', $t['track_corrected']);
		$artist_node = $root->addChild('artist', repamp($t['artist']));
		$artist_node->addAttribute('corrected', $t['artist_corrected']);
		$album_node = $root->addChild('album', repamp($t['album']));
		$album_node->addAttribute('corrected', $t['album_corrected']);
		$albumartist_node = $root->addChild('albumArtist', repamp($t['albumartist']));
		$albumartist_node->addAttribute('corrected', $t['albumartist_corrected']);
		$ignoredmessage_node = $root->addChild('ignoredMessage', $t['ignored_message']);
		$ignoredmessage_node->addAttribute('code', $t['ignored_code']);

		return $xml;
	}


	public static function scrobble($userid, $artist, $track, $timestamp, $album, $context, $streamid, $chosenbyuser, $tracknumber, $mbid, $albumartist, $duration, $api_key) {
		global $adodb;

		$sessionid = getScrobbleSessionID($userid, $api_key);

		$accepted_count = 0;
		$ignored_count = 0;
		$tracks_array = array();

		// Convert input to track arrays and add them to tracks_array
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
			if ($t['ignoredcode'] === 0) {
				try {
					// Create artist, album and track if not already in db
					$t['track_id'] = getTrackID($t['artist'], $t['album'], $t['track'], $t['mbid'], $t['duration']);
					$t['scrobbletrack_id'] = getScrobbleTrackID($t['artist'], $t['album'], $t['track'], $t['mbid'], $t['duration'], $t['track_id']);
				} catch (Exception $e) {
					// Roll back database entries, log error and respond with error message
					$adodb->FailTrans();
					$adodb->CompleteTrans();
					reportError($e->getMessage(), $e->getTraceAsString());
					return XML::error('failed', '16', 'The service is temporarily unavailable, please try again.');
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
					return XML::error('failed', '16', 'The service is temporarily unavailable, please try again.');
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
				if ($t['ignoredcode'] === 0) {
					/* Forward scrobbles, we are forwarding unmodified input submitted by user,
					 * but only the scrobbles that passed our ignore filters, see prepareTrack(). */
					forwardScrobble($userid,
						$t['artist_old'],
						$t['album_old'],
						$t['track_old'],
						$t['timestamp_old'],
						$t['mbid_old'],
						'',
						'',
						$t['duration_old']);
				}
			}
		}

		// Build xml
		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('scrobbles', null);

		for ($i = 0; $i < count($tracks_array); $i++) {
			$t = $tracks_array[$i];

			$scrobble = $root->addChild('scrobble', null);
			$track_node = $scrobble->addChild('track', repamp($t['track']));
			$track_node->addAttribute('corrected', $t['track_corrected']);
			$artist_node = $scrobble->addChild('artist', repamp($t['artist']));
			$artist_node->addAttribute('corrected', $t['artist_corrected']);
			$album_node = $scrobble->addChild('album', repamp($t['album']));
			$album_node->addAttribute('corrected', $t['album_corrected']);
			$albumartist_node = $scrobble->addChild('albumArtist', repamp($t['albumartist']));
			$albumartist_node->addAttribute('corrected', $t['albumartist_corrected']);
			$scrobble->addChild('timestamp', $t['timestamp']);
			$ignoredmessage_node = $scrobble->addChild('ignoredMessage', $t['ignoredmessage']);
			$ignoredmessage_node->addAttribute('code', $t['ignoredcode']);

			if ($t['ignoredcode'] === 0) {
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
