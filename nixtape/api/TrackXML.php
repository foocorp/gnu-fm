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

	public static function removeTag($userid, $artist, $trackName, $tag) {
		try {
			$track = new Track($trackName, $artist);
			$track->removeTag($tag, $userid);
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

	public static function getTopFans($name, $artistname, $limit, $cache) {
		global $adodb;

		try {
			$track = new Track($name, $artistname);
			$res = $track->getTopListeners($limit, 0, False, null, null, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('topfans', null);
		$root->addAttribute('artist', $track->artist_name);
		$root->addAttribute('track', $track->name);

		$i = $offset + 1;
		foreach($res as &$row) {
			try {
				$user = new User($row['username']);
				$user_node = $root->addChild('user', null);
				$user_node->addChild('name', $user->name);
				$user_node->addChild('realname', $user->fullname);
				$user_node->addChild('url', repamp($user->getURL()));
				$image_small = $user_node->addChild('image', null);
				$image_small->addAttribute('size', 'small');
				$image_medium = $user_node->addChild('image', null);
				$image_medium->addAttribute('size', 'medium');
				$image_large = $user_node->addChild('image', null);
				$image_large->addAttribute('size', 'large');
				$user_node->addChild('weight', $row['freq']);
			} catch (Exception $e) {}
			$i++;
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
		try {
			$track = new Track($name, $artist);
			$res = $track->love($userid);
		} catch (Exception $e) {
			return XML::error('failed', '7', 'Invalid resource specified');
		}

		if(!$res) {
			$xml = XML::error('failed', '7', 'Invalid resource specified');
		} else {
			$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		}
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
		try {
			$track = new Track($name, $artist);
			$res = $track->unlove($userid);
		} catch (Exception $e) {
			return XML::error('failed', '7', 'Invalid resource specified');
		}

		if(!$res) {
			$xml = XML::error('failed', '7', 'Invalid resource specified');
		} else {
			$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		}
		return $xml;
	}

}
