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


	public static function updateNowPlaying($userid, $artist, $track, $album, $trackNumber, $context, $mbid, $duration, $albumArtist) {
		global $adodb;

		list($artist, $artist_corrected) = correctInput($artist, 'artist');
		list($track, $track_corrected) = correctInput($track,  'track');
		list($album, $album_corrected) = correctInput($album, 'album');
		list($mbid, $mbid_corrected) = correctInput($mbid, 'mbid');
		list($duration, $duration_corrected) = correctInput($duration, 'duration');

		list($ignored_code, $ignored_message) = ignoreInput($artist, $track);

		// Get a scrobble session id
		$sessionid = getOrCreateScrobbleSession($userid);

		// Delete last played track
		$query = 'DELETE FROM Now_Playing WHERE sessionid = ?';
		$params = array($sessionid);
		$adodb->Execute($query, $params);

		//calculate expiry time
		if (!$duration || ($duration > 5400)) {
			// Default expiry time of 5 minutes if $duration is false or above 5400
			$expires = time() + 300;
		} else {
			$expires = time() + $duration;
		}

		if (!$ignored_code) {
			// Create artist, album, track if not in db
			getTrackCreateIfNew($artist, $album, $track, $mbid, $duration);

			// Add new track to database
			$query = 'INSERT INTO Now_Playing(sessionid, track, artist, album, mbid, expires) VALUES (?,?,?,?,?,?)';
			$params = array($sessionid, $track, $artist, $album, $mbid, $expires);
			$adodb->Execute($query, $params);

			//TODO Clean up expired tracks in now_playing table
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
		/* For dev purposes only
		$duration_node = $root->addChild('duration', $duration);
		$duration_node->addAttribute('corrected', $duration_corrected);
		$expires_node = $root->addChild('expires', $expires - time());
		/**/

		return $xml;
	}


	public static function scrobble($userid, $artist, $track, $timestamp) {
		global $base_url;

		if(empty($artist) || empty($track) || empty($timestamp)) {
			return(XML::error('failed', '6', 'Required parameters are empty'));
		}

		$user = User::new_from_uniqueid_number($userid);
		$session_id = $user->getScrobbleSession();

		$post_vars = array(
			'a[0]' => $artist,
			't[0]' => $track,
			'i[0]' => $timestamp,
			's' => $session_id
		);

		$url = $base_url . '/scrobble-proxy.php?method=scrobble';
		$mysession = curl_init($url);
		curl_setopt($mysession, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($mysession, CURLOPT_POST, true);
		curl_setopt($mysession, CURLOPT_POSTFIELDS, $post_vars);

		$response = curl_exec($mysession);
		curl_close($mysession);

		if($response == "OK\n1") {
			$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
			$root = $xml->addChild('scrobbles', null);
			$scrobble = $root->addChild('scrobble', null);
			$scrobble->addChild('track', repamp($track));
			$scrobble->addChild('artist', repamp($artist));
			$scrobble->addChild('timestamp', $timestamp);
		}else{
			$xml = new SimpleXMLElement('<lfm status="failed"></lfm>');
		}

		return $xml;
	}

}
