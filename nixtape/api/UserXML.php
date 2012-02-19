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
require_once($install_path . '/data/User.php');
require_once('xml.php');

class UserXML {

	public static function getInfo($username) {

		try {
			$user = new User($username);
		} catch (Exception $e) {
			return XML::error('failed', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$user_node = $xml->addChild('user', null);
		$user_node->addChild('name', $user->name);
		$user_node->addChild('homepage', $user->homepage);
		$user_node->addChild('location', $user->location);
		$user_node->addChild('bio', $user->bio);
		$user_node->addChild('url', $user->getURL());
		$user_node->addChild('playcount', $user->getTotalTracks());
		$user_node->addChild('profile_created', strftime('%c', $user->created));
		if (isset($user->modified)) {
			$user_node->addChild('profile_updated', strftime('%c', $user->modified));
		}

		return $xml;
	}

	public static function getTopArtists($username, $limit, $streamable, $page, $period, $cache) {
		global $adodb;

		try {
			$timestamp = UserXML::_periodToTimestamp($period);
		} catch (Exception $e) {
			return(XML::error('error', '13', 'Invalid method signature supplied'));
		}

		$offset = ($page - 1) * $limit;
		$begin = $timestamp - ($timestamp % 3600);

		try {
			$user = new User($username);
			$res = $user->getTopArtists($limit, $offset, $streamable, $begin, null, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$query = 'SELECT COUNT(DISTINCT(artist)) FROM Scrobbles s';

		if($streamable) {
			$query .= ' INNER JOIN Artist a ON s.artist=a.name WHERE a.streamable=1';
			$andquery = True;
		} else {
			$query .= ' WHERE';
			$andquery = False;
		}

		if ($begin) {
			$andquery ? $query .= ' AND' : $andquery = True;
			$query .= ' time>' . $begin;
		}

		$andquery ? $query .= ' AND' : null;
		$query .= ' userid=' . $user->uniqueid;

		$total = $adodb->CacheGetOne($cache, $query);
		$totalPages = ceil($total/$limit);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('topartists', null);
		$root->addAttribute('user', $user->name);
		$root->addAttribute('type', $period);
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', $totalPages);
		$root->addAttribute('total', $total);

		$i = $offset + 1;
		foreach($res as &$row) {
			$artist_node = $root->addChild('artist', null);
			$artist_node->addAttribute('rank', $i);
			$artist_node->addChild('name', repamp($row['artist']));
			$artist_node->addChild('playcount', $row['freq']);
			try {
				$artist = new Artist($row['artist']);
				$artist_node->addChild('mbid', $artist->mbid);
				$artist_node->addChild('url', $artist->geturl());
				$artist_node->addChild('streamable', $artist->streamable);
				$image_small = $artist_node->addChild('image', $artist->image_small);
				$image_small->addAttribute('size', 'small');
				$image_medium = $artist_node->addChild('image', $artist->image_medium);
				$image_medium->addAttribute('size', 'medium');
				$image_large = $artist_node->addChild('image', $artist->image_large);
				$image_large->addAttribute('size', 'large');
			} catch (Exception $e) {}
			$i++;
		}

		return $xml;
	}

	public static function getTopTracks($username, $limit, $streamable, $page, $period, $cache) {
		global $adodb;

		try {
			$timestamp = UserXML::_periodToTimestamp($period);
		} catch (Exception $e) {
			return(XML::error('error', '13', 'Invalid method signature supplied'));
		}

		$offset = ($page - 1) * $limit;
		$begin = $timestamp - ($timestamp % 3600);

		try {
			$user = new User($username);
			$res = $user->getTopTracks($limit, $offset, $streamable, $begin, null, null, $cache);
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
		if ($begin) {
			$andquery ? $query .= ' AND' : $andquery = True;
			$query .= ' time>' . $begin;
		}
		$andquery ? $query .= ' AND' : null;
		$query .= ' userid=' . $user->uniqueid . ' GROUP BY s.track, s.artist) c';
		$total = $adodb->CacheGetOne($cache, $query);

		$totalPages = ceil($total/$limit);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptracks', null);
		$root->addAttribute('user', $user->name);
		$root->addAttribute('type', $period);
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', $totalPages);
		$root->addAttribute('total', $total);

		$i = $offset + 1;
		foreach($res as &$row) {
			try {
				$track = new Track($row['track'], $row['artist']);
				$artist = $track->getArtist();
				$track_node = $root->addChild('track', null);
				$track_node->addAttribute('rank', $i);
				$track_node->addChild('name', repamp($track->name));
				$track_node->addChild('duration', $track->duration);
				$track_node->addChild('playcount', $row['freq']);
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

	public static function getRecentTracks($u, $limit, $page) {
		global $adodb;

		if (!isset($limit)) {
			$limit = 10;
		}

		$offset = ($page - 1) * $limit;
		$err = 0;
		try {
			$user = new User($u);
			if ($page == 1) {
				$npres = $user->getNowPlaying(1);
			}
			$res = $user->getScrobbles($limit, $offset);
		} catch (Exception $e) {
			$err = 1;
		}

		if ($err || !$res) {
			return(XML::error('error', '7', 'Invalid resource specified'));
		}

		$totalTracks = $user->getTotalTracks();
		$totalPages = ceil($totalTracks / $limit);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('recenttracks', null);
		$root->addAttribute('user', $user->name);
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', $totalPages);

		if ($npres) {
			foreach ($npres as &$row) {
				$track = $root->addChild('track');
				$track->addAttribute('nowplaying', 'true');
				$row['time'] = time();
				UserXML::_addTrackDetails($track, $row);
			}
		}

		foreach ($res as &$row) {
			$track = $root->addChild('track', null);
			UserXML::_addTrackDetails($track, $row);
		}

		return $xml;
	}

	private static function _addTrackDetails($track, $row) {
		$artist = $track->addChild('artist', repamp($row['artist']));
		$artist->addAttribute('mbid', $row['artist_mbid']);
		$name = $track->addChild('name', repamp($row['track']));
		$track->addChild('mbid', $row['mbid']);
		$album = $track->addChild('album', repamp($row['album']));
		$album->addAttribute('mbid', $row['album_mbid']);
		$track->addChild('url', repamp(Server::getTrackURL($row['artist'], $row['album'], $row['track'])));
		$date = $track->addChild('date', gmdate('d M Y H:i', $row['time']) . ' GMT');
		$date->addAttribute('uts', $row['time']);
		$track->addChild('streamable', null);
	}

	private static function _periodToTimestamp($period) {
		//TODO: Do better, this is too ugly :\
		if (strcmp($period, 'overall') == 0) {
			$timestamp = 0;
		} else if (strcmp($period, '7day') == 0) {
			$timestamp = strtotime('-7 days');
		} else if (strcmp($period, '1month') == 0) {
			$timestamp = strtotime('-1 month');
		} else if (strcmp($period, '3month') == 0) {
			$timestamp = strtotime('-3 months');
		} else if (strcmp($period, '6month') == 0) {
			$timestamp = strtotime('-6 months');
		} else if (strcmp($period, '12month') == 0) {
			$timestamp = strtotime('-12 months');
		} else {
			throw new Exception("Not a valid period");
		}
		return $timestamp;
	}

	public static function getTopTags($u, $limit, $cache) {
		global $base_url;

		try {
			$user = new User($u);
			$res = $user->getTopTags($limit, 0, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		if(!$res) {
			return XML::error('error', '6', 'No tags for this user');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptags');
		$root->addAttribute('user', $user->name);

		foreach ($res as &$row) {
			$tag = $root->addChild('tag', null);
			$tag->addChild('name', repamp($row['tag']));
			$tag->addChild('count', repamp($row['freq']));
			$tag->addChild('url', Server::getTagURL($row['tag']));
		}

		return $xml;
	}

	public static function getPersonalTags($u, $tag, $taggingtype, $limit, $page, $cache, $streamable) {

		$offset = ($page - 1) * $limit;

		try {
			$user = new User($u);
			$res = $user->getPersonalTags($tag, $taggingtype, $limit, $offset, $cache, $streamable);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		if(!$res) {
			return XML::error('error', '6', 'No tag with this name');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('taggings');
		$root->addAttribute('user', repamp($user->name));
		$root->addAttribute('tag', repamp($tag));
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', null); //TODO
		$root->addAttribute('total', null); //TODO

		if(strtolower($taggingtype) == 'artist') {
			$artists = $root->addChild('artists', null);
			foreach($res as &$row) {
				$artist = new Artist($row['artist']);
				$artist_node = $artists->addChild('artist', null);
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
			}
		}elseif(strtolower($taggingtype) == 'album') {
			$albums = $root->addChild('albums', null);
			foreach($res as &$row) {
				$album = new Album($row['album'], $row['artist']);
				$album_node = $albums->addChild('album', null);
				$album_node->addChild('name', repamp($album->name));
				$album_node->addChild('mbid', $album->mbid);
				$album_node->addChild('url', $album->getURL());

				$artist = new Artist($row['artist']);
				$artist_node = $album_node->addChild('artist', null);
				$artist_node->addChild('name', repamp($artist->name));
				$artist_node->addChild('mbid', $artist->mbid);
				$artist_node->addChild('url', $artist->getURL());
				$album_node->addChild('image', $album->image);
			}
		}elseif(strtolower($taggingtype) == 'track') {
			$tracks = $root->addChild('tracks', null);
			foreach($res as &$row) {
				$track = new Track($row['track'], $row['artist']);
				$track_node = $tracks->addChild('track', null);
				$track_node->addChild('name', repamp($track->name));
				$track_node->addChild('duration', $track->duration);
				$track_node->addChild('mbid', $track->mbid);
				$track_node->addChild('url', $track->getURL());
				$track_node->addChild('streamable', $track->streamable);

				$artist = new Artist($row['artist']);
				$artist_node = $track_node->addChild('artist', null);
				$artist_node->addChild('name', repamp($artist->name));
				$artist_node->addChild('mbid', $artist->mbid);
				$artist_node->addChild('url', $artist->getURL());
				$image_small = $track_node->addchild('image', $artist->image_small);
				$image_small->addAttribute('size', 'small');
				$image_medium = $track_node->addchild('image', $artist->image_medium);
				$image_medium->addAttribute('size', 'medium');
				$image_large = $track_node->addchild('image', $artist->image_large);
				$image_large->addAttribute('size', 'large');
			}

		} else {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		return $xml;
	}

	public static function getTagInfo($u, $tag, $cache) {

		try {
			$user = new User($u);
			$res = $user->getTagInfo($tag, $cache);
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

	public static function getLovedTracks($username, $limit = 50, $page = 1, $streamable = False, $cache = 600) {
		global $adodb;

		$offset = ($page - 1) * $limit;
		try {
			$user = new User($username);
			$res = $user->getLovedTracks($limit, $offset, $streamable, null, $cache);
		} catch (Exception $ex) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		// Get total track count, using subquery to get distinct row(artist, track) count
		$query = 'SELECT COUNT(*) FROM (SELECT count(*) FROM Loved_Tracks lt';

		if($streamable) {
			$query .= ' WHERE ROW(lt.artist, lt.track) IN (SELECT artist_name, name FROM Track WHERE streamable=1)';
			$andquery = True;
		} else {
			$query .= ' WHERE';
			$andquery = False;
		}

		$andquery ? $query .= ' AND' : null;
		$query .= ' userid=' . $user->uniqueid . ' GROUP BY lt.track, lt.artist) c';
		$total = $adodb->CacheGetOne($cache, $query);

		$totalPages = ceil($total/$limit);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('lovedtracks');
		$root->addAttribute('user', $user->name);
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', $totalPages);
		$root->addAttribute('total', $total);

		foreach ($res as &$row) {
			$track_node = $root->addChild('track', null);
			UserXML::_addLBTrackDetails($track_node, $row);
		}

		return $xml;
	}

	public static function getBannedTracks($u, $limit = 50, $page = 1) {
		global $adodb;

		$offset = ($page - 1) * $limit;	
		try {
			$user = new User($u);
			$res = $user->getBannedTracks($limit, $offset);
		} catch (Exception $ex) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$totalPages = $adodb->GetOne('SELECT COUNT(track) FROM Banned_Tracks WHERE userid = ' . $user->uniqueid);
		$totalPages = ceil($totalPages / $limit);

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('bannedtracks');
		$root->addAttribute('user', $user->name);
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		$root->addAttribute('totalPages', $totalPages);

		foreach ($res as &$row) {
			$track_node = $root->addChild('track', null);
			UserXML::_addLBTrackDetails($track_node, $row);
		}

		return $xml;
	}

	private static function _addLBTrackDetails($track_node, $row) {
		$track = new Track($row['track'], $row['artist']);
		$track_node->addChild('name', repamp($track->name));
		$track_node->addChild('mbid', $track->mbid);
		$track_node->addChild('url', $track->getURL());
		$date = $track_node->addChild('date', gmdate('d M Y H:i', $row['time']) . ' GMT');
		$date->addAttribute('uts', $row['time']);
		try {
			$artist = new Artist($row['artist']);
			$artist_node = $track_node->addChild('artist', null);
			$artist_node->addChild('name', repamp($artist->name));
			$artist_node->addChild('mbid', $artist->mbid);
			$artist_node->addChild('url', $artist->getURL());
		} catch (Exception $e) {}
	}

	public static function getNeighbours($u, $limit = 50) {
		try {
			$user = new User($u);
			$res = $user->getNeighbours($limit);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('neighbours');
		$root->addAttribute('user', $user->name);

		if (empty($res)) {
			return $xml;
		}

		$highest_match = $res[0]['shared_artists'];

		foreach ($res as $row) {
			$neighbour = $row['user'];
			$user_node = $root->addChild('user', null);
			$user_node->addChild('name', repamp($neighbour->name));
			$user_node->addChild('fullname', repamp($neighbour->fullname));
			$user_node->addChild('url', repamp($neighbour->getURL()));
			// Give a normalised value
			$user_node->addChild('match', $row['shared_artists'] / $highest_match);
		}

		return $xml;
	}

}
