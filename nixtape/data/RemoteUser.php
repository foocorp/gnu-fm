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
require_once($install_path . '/data/sanitize.php');
require_once($install_path . '/utils/human-time.php');
require_once($install_path . '/data/Server.php');
require_once($install_path . '/data/Tag.php');
require_once($install_path . '/data/User.php');

/**
 * Represents User data
 *
 * General attributes are accessible as public variables.
 *
 */
class RemoteUser extends User {

	public $domain;
	public $lastfm = false;

	/**
	 * User constructor
	 *
	 * @param string $name The name of the user to load
	 */
	function __construct($name, $data = null) {
		global $base_url, $lastfm_key;

		$base = preg_replace('#/$#', '', $base_url);

		$components = explode("@", $name, 2);
		$this->username = $components[0];
		$this->domain = $components[1];

		if (strstr($this->domain, 'last.fm') || strstr($this->domain, 'ws.audioscrobbler.com')) {
			$this->domain = 'ws.audioscrobbler.com';
			$this->lastfm = true;
		}

		if ($this->lastfm && !isset($lastfm_key)) {
			throw new Exception('This server isn\'t configured to communicate with Last.fm');
		}

		if (is_array($data)) {
			$row = $data;
		} else {
			global $adodb;
		
			$row = $this->get_xml('?method=user.getInfo&user=' . $this->username);
			if (!isset($row->user)) {
				throw new Exception('EUSER', 22);
			} else {
				$row = $row->user;
			}
		}

		$this->name             = $name;
		$this->email            = $row->email;
		$this->fullname         = $row->realname;
		$this->url		= $row->url;
		$this->homepage         = $row->homepage;
		$this->bio              = $row->bio;
		$this->location         = $row->location;
		$this->location_uri     = $row->location_uri;
		$this->id               = $row->webid_uri;
		$this->webid_uri        = $row->webid_uri;
		$this->avatar_uri       = $row->image[2];
		$this->laconica_profile = $row->laconica_profile;
		$this->journal_rss      = $row->journal_rss;
		$this->acctid           = $row->url . '#acct';
		$this->created          = $row->created;
		$this->modified         = $row->modified;
		$this->playcount	= $row->playcount;
		if (!preg_match('/\:/', $this->id)) {
			$this->id = $this->getURL() . '#me';
		}
	}

	function save() {
		throw Exception("Unable to save remote user");
	}

	/**
	 * Get a user's scrobbles ordered by time
	 *
	 * @param int $number The number of scrobbles to return
	 * @param int $offset The position of the first scrobble to return
	 * @return array An array of scrobbles with human time
	 */
	function getScrobbles($number, $offset = 0) {
		$page = (int) $offset / $number + 1;
		$xml = $this->get_xml('?method=user.getRecentTracks&user=' . $this->username . '&limit=' . $number . '&page=' . $page);
		$tracks = array();
		foreach($xml->recenttracks->track as $xmltrack) {
			$track = array();
			$track['artist'] = $xmltrack->artist;
			$track['album'] = $xmltrack->album;
			$track['track'] = $xmltrack->name;
			$track['artisturl'] = $xmltrack->url;
			$track['albumurl'] = $xmltrack->url;
			$track['trackurl'] = $xmltrack->url;
			$track['time'] = (int) $xmltrack->date->attributes()->uts;
			$track['timehuman'] = human_timestamp($track['time']);
			$tracks[] = $track;
		}
		return $tracks;
	}

	/**
	 * Retrieve a user's avatar via the gravatar service
	 *
	 * @param int $size The desired size of the avatar (between 1 and 512 pixels)
	 * @return array A URL to the user's avatar image
	 */
	function getAvatar($size = 64) {
		if (!empty($this->avatar_uri)) {
			return $this->avatar_uri;
		}

		return 'http://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?s=' . $size . '&d=monsterid';
	}

	function getURL($component = 'profile', $params = false) {
		return Server::getUserURL($this->name, $component, $params);
	}

	/**
	 * Get a user's now-playing tracks
	 *
	 * @return array An array of nowplaying data
	 */
	function getNowPlaying($number) {
		return array();
	}

	/**
	 * Log in to the gnukebox server
	 *
	 * @return array A string containing the session key to be used for scrobbling
	 */
	function getScrobbleSession() {
		return false;
	}

	/**
	 * Log in to the radio server
	 *
	 * @param string $station The station to be played
	 * @return array A string containing the session key to be used for streaming
	 */
	function getRadioSession($station) {
		return false;
	}

	/**
	 * Log in to the web services
	 *
	 * @return array A string containing the web session key
	 */
	function getWebServiceSession() {
		return false;
	}

	/**
	 * Get this user's top artists
	 *
	 * @param int $limit The number of artists to return
	 * @param int $offset Skip this number of rows before returning artists
	 * @param bool $streamable Only return streamable artists
	 * @param int $begin Only use scrobbles with time higher than this timestamp
	 * @param int $end Only use scrobbles with time lower than this timestamp
	 * @param int $cache Caching period in seconds
	 * @return array An array of artists ((artist, freq, artisturl) ..) or empty array in case of failure
	 */
	function getTopArtists($limit = 20, $offset = 0, $streamable = False, $begin = null, $end = null, $cache = 600) {
		return array();
	}

	/**
	 * Get this user's top tracks
	 *
	 * @param int $limit The number of tracks to return
	 * @param int $offset Skip this number of rows before returning tracks
	 * @param bool $streamable Only return streamable tracks
	 * @param int $begin Only use scrobbles with time higher than this timestamp
	 * @param int $end Only use scrobbles with time lower than this timestamp
	 * @param int $cache Caching period in seconds
	 * @return array An array of tracks ((artist, track, freq, listeners, artisturl, trackurl) ..) or empty array in case of failure
	 */
	function getTopTracks($limit = 20, $offset = 0, $streamable = False, $begin = null, $end = null, $cache = 600) {
		return array();
	}

	public function getTotalTracks($since = null) {
		return $this->playcount;
	}

	/**
	 * Get a user's top tags, ordered by tag count
	 *
	 * @param int $limit The number of tags to return (default is 10)
	 * @param int $offset The position of the first tag to return (default is 0)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return An array of tag details ((tag, freq) .. )
	 */
	function getTopTags($limit=10, $offset=0, $cache=600) {
		return array();
	}

	/**
	 * Get artists, albums, or tracks tagged with tag by user
	 *
	 * @param string $tag Items are tagged by this tag
	 * $param string $taggingtype Type of tags to return (artist|album|track)
	 * @param int $limit The number of items to return (default is 10)
	 * @param int $offset The position of the first item to return (default is 0)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @param bool $streamable Show only content by streamable artists (default is False)
	 * @return An array of item details ((artist, .. , freq) .. )
	 */

	function getPersonalTags($tag, $taggingtype, $limit=10, $offset=0, $cache=600, $streamable=False) {
		if(isset($tag) and isset($taggingtype)) {
			return array();
		}
	}

	/**
	 * Get a user's tags for a specific artist
	 *
	 * @param string $artist The name of the artist to fetch tags for
	 * @param int $limit The number of tags to return (default is 10)
	 * @param int $offset The position of the first tag to return (default is 0)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return An array of tag details ((tag, freq) .. )
	 */
	function getTagsForArtist($artist, $limit=10, $offset=0, $cache=0) {
		if(isset($artist)) {
			return array();
		}
	}

	/**
	 * Get tag count for tag and user
	 *
	 * @param string $tag The tag to show user's tag count for
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return An array of tag details ((tag, freq) .. )
	 */
	function getTagInfo($tag, $cache=600) {
		if(isset($tag)) {
			return array();
		}
	}

	/**
	 * Retrieves a list of user's loved tracks
	 *
	 * @param int $limit The number of tracks to return
	 * @param int $offset Skip this number of rows before returning tracks
	 * @param bool $streamable Only return streamable tracks
	 * @param int $artist Only return results from this artist
	 * @param int $cache Caching period in seconds
	 * @return array An array of tracks ((artist, track, freq, listeners, artisturl, trackurl) ..) or empty array in case of failure
	 */
	function getLovedTracks($limit = 20, $offset = 0, $streamable = False, $artist = null, $cache = 600) {
		return array();
	}

	/**
	 * Retrieves a list of user's loved artists
	 *
	 * @param int $limit The number of artists to return
	 * @param int $offset Skip this number of rows before returning artists
	 * @param bool $streamable Only return streamable artists
	 * @param int $cache Caching period in seconds
	 * @return array An array of artists ((artist, freq, artisturl) ..) or empty array in case of failure
	 */
	function getLovedArtists($limit = 20, $offset = 0, $streamable = False, $cache = 600) {
		return array();
	}

	/**
	 * Get a user's banned tracks
	 *
	 * @param int $limit The number of tracks to return (defaults to 50)
	 * @return array An array of track details
	 */
	function getBannedTracks($limit = 50, $offset = 0) {
		return array();
	}


	/**
	 * Get details of any connections this user has setup to other services.
	 *
	 * @return array An array of service connection details
	 */
	function getConnections() {
		return array();
	}

	/**
	 * Get artists recommended for this user
	 *
	 * @param int $limit The number of artists to return (defaults to 10)
	 * @param bool $randomised Pick artists at random
	 * @return array An array of artist details
	 */
	function getRecommended($limit = 10, $random = false) {
		global $adodb;

		$loved = $this->getLovedTracks(50);
		$artists = array();
		for ($i = 0; $i < min($limit, count($loved) - 1); $i++) {
			if ($random) {
				$n = rand(0, count($loved) - 1);
			} else {
				$n = $i;
			}
			$artists[] = $loved[$n]['artist'];
		}

		$recommendedArtists = array();
		foreach ($artists as $artist_name) {
			try {
				$artist = new Artist($artist_name);
			} catch (Exception $e) {
				continue;
			}
			$similar = $artist->getSimilar(5);
			foreach ($similar as $sa) {
				if (!array_key_exists($sa['artist'], $recommendedArtists)) {
					$recommendedArtists[$sa['artist']] = $sa;
				}
			}
		}

		$limit = min($limit, count($recommendedArtists) - 1);
		if ($random) {
			$randomArtists = array();
			$keys = array_keys($recommendedArtists);
			for ($i = 0; $i < $limit; $i++) {
				$randomArtists[] = $recommendedArtists[$keys[rand(0, count($recommendedArtists) - 1)]];
			}
			return $randomArtists;
		} else {
			return array_slice($recommendedArtists, 0, $limit);
		}
	}

	/**
	 * Determines whether a user has permission to manage an artist
	 *
	 * @oaram string $artist The name of the artist to check
	 * @return bool Boolean indicating whether this user can edit the artist or not.
	 */
	function manages($artist) {
		return false;
	}

	/**
	 * Checks whether this user has any loved tracks
	 *
	 * @return bool Boolean indicating whether this user has marked any tracks as being loved in the past.
	 */
	function hasLoved() {
		return false;
	}

	/**
	 * Find the neighbours of this user based on the number of loved artists shared between them and other users.
	 *
	 * @param int The number of neighbours to return (defaults to 10).
	 * @return array An array of userids, User objects and the number of loved artists shared with this user.
	 */
	function getNeighbours($limit=10) {
		return array();
	}

	function get_xml($params) {
		global $lastfm_key;

		$wsurl = 'http://' . $this->domain . '/2.0/' . $params;
		if ($this->lastfm) {
			$wsurl .= '&api_key=' . $lastfm_key;
		}
		return simplexml_load_file($wsurl);
	}

}
