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
require_once($install_path . '/data/Tag.php');
require_once($install_path . '/data/Server.php');
require_once('xml.php');

class TagXML {

	public static function getTopTags($limit, $cache) {

		try {
			$res = Tag::getTopTags($limit, 0, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptags');
		foreach($res as &$row) {
			$tag_node = $root->addChild('tag');
			$tag_node->addChild('name', repamp($row['tag']));
			$tag_node->addChild('count', $row['freq']);
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;

	}

	public static function getTopArtists($tag, $limit, $page, $streamable, $cache) {

		$offset = ($page - 1) * $limit;

		try {
			$res = Tag::getTopArtists($tag, $limit, $offset, $streamable, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		if(!$res) {
			return XML::error('error', '6', 'No tag with that name');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('topartists');
		$root->addAttribute('tag', repamp($tag));
		$i = $offset + 1;
		foreach($res as &$row) {
			$artist_node = $root->addChild('artist');
			$artist_node->addAttribute('rank', $i);
			$artist = new Artist($row['artist']);
			$artist_node->addChild('name', repamp($artist->name));
			$artist_node->addChild('mbid', $artist->mbid);
			$artist_node->addChild('url', $artist->getURL());
			$artist_node->addChild('streamable', $artist->streamable);
			$image_small = $artist_node->addchild('image', $artist->image_small);
			$image_small->addAttribute('size', 'small');
			$image_medium = $artist_node->addchild('image', $artist->image_medium);
			$image_medium->addAttribute('size', 'medium');
			$image_large = $artist_node->addchild('image', $artist->image_large);
			$image_large->addAttribute('size', 'large');
			$i++;
		}

		return $xml;

	}

	public static function getTopAlbums($tag, $limit, $page, $streamable, $cache) {

		$offset = ($page - 1) * $limit;

		try {
			$res = Tag::getTopAlbums($tag, $limit, $offset, $streamable, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		if(!$res) {
			return XML::error('error', '6', 'No tag with that name');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('topalbums');
		$root->addAttribute('tag', repamp($tag));
		$i = $offset + 1;
		foreach($res as &$row) {
			$album_node = $root->addChild('album');
			$album_node->addAttribute('rank', $i);

			$album = new Album($row['album'], $row['artist']);
			$album_node->addChild('name', repamp($album->name));
			$album_node->addChild('mbid', $album->mbid);
			$album_node->addChild('url', $album->getURL());

			$artist = new Artist($album->artist_name);
			$artist_node = $album_node->addChild('artist');
			$artist_node->addChild('name', repamp($artist->name));
			$artist_node->addChild('mbid', $artist->mbid);
			$artist_node->addChild('url', $artist->getURL());
			$album_node->addchild('image', $album->image);
			$i++;
		}

		return $xml;
	}

	public static function getTopTracks($tag, $limit, $page, $streamable, $cache) {

		$offset = ($page - 1) * $limit;

		try {
			$res = Tag::getTopTracks($tag, $limit, $offset, $streamable, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		if(!$res) {
			return XML::error('error', '6', 'No tag with that name');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptracks');
		$root->addAttribute('tag', repamp($tag));
		$i = $offset + 1;
		foreach($res as &$row) {
			$track_node = $root->addChild('track');
			$track_node->addAttribute('rank', $i);

			$track = new Track($row['track'], $row['artist']);
			$track_node->addChild('name', repamp($track->name));
			$track_node->addChild('duration', $track->duration);
			$track_node->addChild('mbid', $track->mbid);
			$track_node->addChild('url', $track->getURL());
			$track_node->addChild('streamable', $track->streamable);

			$artist = new Artist($track->artist_name);
			$artist_node = $track_node->addChild('artist');
			$artist_node->addChild('name', repamp($artist->name));
			$artist_node->addChild('mbid', $artist->mbid);
			$artist_node->addChild('url', $artist->getURL());
			$image_small = $track_node->addchild('image', $artist->image_small);
			$image_small->addAttribute('size', 'small');
			$image_medium = $track_node->addchild('image', $artist->image_medium);
			$image_medium->addAttribute('size', 'medium');
			$image_large = $track_node->addchild('image', $artist->image_large);
			$image_large->addAttribute('size', 'large');
			$i++;
		}

		return $xml;
	}

	public static function getInfo($tag, $cache) {

		try {
			$res = Tag::getInfo($tag, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		if(!$res) {
			return XML::error('error', '6', 'No tag with that name');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('tag');
		$root->addChild('name', repamp($tag));
		$root->addChild('url', Server::getTagURL($tag));
		$root->addChild('taggings', $res[0]['freq']);

		return $xml;
	}


	
}

