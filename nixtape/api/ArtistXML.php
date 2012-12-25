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

require_once($install_path . '/data/Artist.php');
require_once('xml.php');

/**
 * Class with functions that returns XML-formatted data for artists.
 *
 * These functions are mainly used by web service methods.
 *
 * @package API
 */
class ArtistXML {

	/**
	 * Provides artist information in XML format
	 *
	 * @param string $api_key A 32 character API key (currently not checked)
	 * @param string $artistName The name of the artist to retrieve info for
	 * @param string $mbid A music brainz ID (optional), if supplied this will be preferred to the artist name
	 * @param string $lang A 2 character ISO 639 alpha-2 code indicating the language to return the information in
	 * @return A SimpleXMLElement containing the artist's information
	 */
	public static function getInfo($artistName, $api_key = false, $mbid = false, $lang = 'en') {
		// We assume $api_key is valid and set at this point

		if (!isset($artistName) && !isset($mbid)) {
			echo XML::error('failed', '7', 'Invalid resource specified');
			return;
		}

		try {
			$artist = new Artist($artistName, $mbid);
		} catch (Exception $e) {
			return XML::error('failed', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');

		$artistXml = $xml->addChild('artist', null);
		$artistXml->addChild('name', $artist->name);
		$artistXml->addChild('mbid', $artist->mbid);
		$artistXml->addChild('url', $artist->getURL());
		$artistXml->addChild('streamable', $artist->streamable);

		$bio = $artistXml->addChild('bio', null);
		$bio->addChild('published', $artist->bio_published);
		$bio->addChild('summary', $artist->bio_summary);
		$bio->addChild('content', $artist->bio_content);

		return $xml;
	}

	public static function getTopTracks($artistname, $limit, $streamable, $page, $cache) {
		global $adodb;

		$offset = ($page - 1) * $limit;

		try {
			$artist = new Artist($artistname);
			$res = $artist->getTopTracks($limit, $offset, $streamable, null, null, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		// Get total track count, using subquery to get distinct row(artist, track) count
		$query = 'SELECT count(*) FROM (SELECT count(*) FROM Scrobbles s';
		if($streamable) {
			$query .= ' WHERE ROW(s.artist, s.track) IN (SELECT artist_name, name FROM Track WHERE streamable=1)';
			$andquery = True;
		} else {
			$query .= ' WHERE';
			$andquery = False;
		}
		$andquery ? $query .= ' AND' : null;
		$query .= ' artist=' . $adodb->qstr($artist->name) . ' GROUP BY s.track, s.artist) c';
		$total = $adodb->CacheGetOne($cache, $query);

		$totalPages = ceil($total/$limit);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptracks', null);
		$root->addAttribute('artist', $artist->name);
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', $totalPages);
		$root->addAttribute('total', $total);

		$i = $offset + 1;
		foreach($res as &$row) {
			try {
				$track = new Track($row['track'], $row['artist']);
				$track_node = $root->addChild('track', null);
				$track_node->addAttribute('rank', $i);
				$track_node->addChild('name', repamp($track->name));
				$track_node->addChild('duration', $track->duration);
				$track_node->addChild('playcount', $row['freq']);
				$track_node->addChild('listeners', $row['listeners']);
				$track_node->addChild('mbid', $track->mbid);
				$track_node->addChild('url', repamp($row['trackurl']));
				$track_node->addChild('streamable', $track->streamable);

				$artist_node = $track_node->addChild('artist', null);
				$artist_node->addChild('name', repamp($artist->name));
				$artist_node->addChild('mbid', $artist->mbid);
				$artist_node->addChild('url', repamp($row['artisturl']));
				$image_small = $track_node->addChild('image', $artist->image_small);
				$image_small->addAttribute('size', 'small');
				$image_medium = $track_node->addChild('image', $artist->image_medium);
				$image_medium->addAttribute('size', 'medium');
				$image_large = $track_node->addChild('image', $artist->image_large);
				$image_large->addAttribute('size', 'large');
			} catch (Exception $e) {}
			$i++;
		}

		return $xml;
	}

	public static function getTopTags($artistName, $limit, $cache) {

		try {
			$artist = new Artist($artistName);
			$res = $artist->getTopTags($limit, 0, $cache);
		} catch (Exception $e) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		if(!$res) {
			return(XML::error('failed', '6', 'No tags for this artist'));
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptags', null);
		$root->addAttribute('artist', $artist->name);

		foreach ($res as &$row) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($row['tag']));
			$tag_node->addChild('count', $row['freq']);
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}

	public static function getTags($artistName, $userid, $limit, $cache) {

		try {
			$artist = new Artist($artistName);
			$res = $artist->getTags($userid, $limit, 0, $cache);
		} catch (Exception $e) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		if(!$res) {
			return(XML::error('failed', '6', 'No tags for this artist'));
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('tags', null);
		$root->addAttribute('artist', repamp($artist->name));

		foreach($res as &$row) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($row['tag']));
			$tag_node->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}



	public static function getFlattr($artistName) {

		try {
			$artist = new Artist($artistName);
		} catch (Exception $e) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('flattr', null);
		$root->addAttribute('artist', $artist->name);
		$root->addChild('flattr_uid', $artist->flattr_uid);

		return $xml;
	}


}
