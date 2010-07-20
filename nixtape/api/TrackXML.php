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

class TrackXML {

	public static function addTags($userid, $artist, $album, $track, $tags) {
		global $adodb;

		$tags = split(",", strtolower($tags));
		foreach($tags as $tag) {
			$tag = trim($tag);
			if (strlen($tag) == 0) {
				continue;
			}
			try {
				$adodb->Execute('INSERT INTO tags VALUES ('
					. $adodb->qstr($tag) . ', '
					. $adodb->qstr($artist) . ', '
					. $adodb->qstr($album) . ', '
					. $adodb->qstr($track) . ', '
					. $userid . ')');
			} catch (exception $ex) {}
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		return $xml;
	}

	public static function getTopTags($artist, $name) {

		$track = new Track($name, $artist);
		$tags = $track->getTopTags();

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		$root = $xml->addChild('toptags', null);
		$root->addAttribute('artist', $artist);
		$root->addAttribute('track', $name);

		foreach($tags as &$tag) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($tag['tag']));
			$tag_node->addChild('count', $tag['freq']);
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}

	public static function getTags($artist, $name, $userid) {

		$track = new Track($name, $artist);
		$tags = $track->getTags($userid);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		$root = $xml->addChild('tags', null);
		$root->addAttribute('artist', $artist);
		$root->addAttribute('track', $name);

		foreach($tags as $tag) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($tag));
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}

	public static function ban($artist, $name, $userid) {
		global $adodb;

		try {
			$res = $adodb->Execute('INSERT INTO banned_tracks VALUES ('
				. $userid . ', '
				. $adodb->qstr($name) . ', '
				. $adodb->qstr($artist) . ', '
				. time() . ")");
		} catch (exception $ex) {}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		return $xml;
	}

	public static function love($artist, $name, $userid) {
		global $adodb;

		try {
			$res = $adodb->Execute('INSERT INTO loved_tracks VALUES ('
				. $userid . ', '
				. $adodb->qstr($name) . ', '
				. $adodb->qstr($artist) . ', '
				. time() . ")");
		} catch (exception $ex) {}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		return $xml;
	}

}
?>
