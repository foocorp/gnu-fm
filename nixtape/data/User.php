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

/**
 * Represents User data
 *
 * General attributes are accessible as public variables.
 *
 */
class User {

	public $name, $email, $fullname, $bio, $location, $homepage, $error, $userlevel;
	public $id, $acctid, $avatar_uri, $location_uri, $webid_uri, $laconica_profile, $journal_rss;
	public $password, $has_identica, $created, $modified, $uniqueid, $anticommercial, $receive_emails;
	public $remote = false;

	/**
	 * User constructor
	 *
	 * @param string $name The name of the user to load
	 * @param array $data User data as row returned from Users database table
	 */
	function __construct($name, $data = null) {

		global $base_url;
		$base = preg_replace('#/$#', '', $base_url);

		if (is_array($data)) {
			$row = $data;
		} else {
			global $adodb;
			$query = 'SELECT * FROM Users WHERE lower(username) = lower(' . $adodb->qstr($name) . ') LIMIT 1';
			$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
			$row = $adodb->CacheGetRow(7200, $query);
			if (!$row) {
				throw new Exception('EUSER', 22);
			}
		}

		if (is_array($row)) {
			$this->name             = $row['username'];
			$this->password         = $row['password'];
			$this->email            = $row['email'];
			$this->fullname         = $row['fullname'];
			$this->homepage         = $row['homepage'];
			$this->bio              = $row['bio'];
			$this->location         = $row['location'];
			$this->location_uri     = $row['location_uri'];
			$this->userlevel        = $row['userlevel'];
			$this->id               = $row['webid_uri'];
			$this->webid_uri        = $row['webid_uri'];
			$this->avatar_uri       = $row['avatar_uri'];
			$this->laconica_profile = $row['laconica_profile'];
			$this->journal_rss      = $row['journal_rss'];
			$this->acctid           = $this->getURL() . '#acct';
			$this->created          = $row['created'];
			$this->modified         = $row['modified'];
			$this->uniqueid         = $row['uniqueid'];
			$this->anticommercial   = $row['anticommercial'];
			$this->receive_emails   = $row['receive_emails'];

			$this->has_identica = preg_match('#^http://identi\.ca/#i', $this->laconica_profile);

			if (!preg_match('/\:/', $this->id)) {
				$this->id = $this->getURL() . '#me';
			}
		}
	}
	/**
	 * Create User object from user id
	 *
	 * @param int $uid User id
	 * @return User User object, or false in case of failure
	 */

	public static function new_from_uniqueid_number($uid) {
		global $adodb;
		$query = sprintf('SELECT * FROM Users WHERE uniqueid = %d', (int)$uid);
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$row = $adodb->CacheGetRow(7200, $query);

		if ($row) {
			try {
				return new User($row['username'], $row);
			} catch (Exception $e) {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Save user data to database
	 *
	 * @return int 1 on success
	 */

	function save() {
		global $adodb;

		// It appears we just discard this data, but this is here for a reason!
		// getLocationDetails will fill in latitude,longitude details into the Places table in the database
		// if it's not already there. This is important as the location_uri field is a foreign key.
		if (!empty($this->location_uri)) {
			$dummy = Server::getLocationDetails($this->location_uri);
		}

		$q = sprintf('UPDATE Users SET '
				. 'email=%s, '     # Send a confirmation email first??
				. 'password=%s, '
				. 'fullname=%s, '
				. 'homepage=%s, '
				. 'bio=%s, '
				. 'location=%s, '
				. 'userlevel=%d, '
				. 'webid_uri=%s, '
				. 'location_uri=%s, '
				. 'avatar_uri=%s, '
				. 'laconica_profile=%s, '
				. 'journal_rss=%s, '
				. 'anticommercial=%d, '
				. 'receive_emails=%d, '
				. 'modified=%d '
				. 'WHERE username=%s'
				, $adodb->qstr($this->email)
				, $adodb->qstr($this->password)
				, $adodb->qstr($this->fullname)
				, $adodb->qstr($this->homepage)
				, $adodb->qstr($this->bio)
				, $adodb->qstr($this->location)
				, $this->userlevel
				, $adodb->qstr($this->id)
				, (empty($this->location_uri) ? 'NULL' : $adodb->qstr($this->location_uri))
				, $adodb->qstr($this->avatar_uri)
				, $adodb->qstr($this->laconica_profile)
				, $adodb->qstr($this->journal_rss)
				, (int)($this->anticommercial)
				, (int)($this->receive_emails)
				, time()
				, $adodb->qstr($this->name));

		try {
			$res = $adodb->Execute($q);
		} catch (Exception $e) {
			header('Content-Type: text/plain');
			exit;
		}

		$query = 'SELECT * FROM Users WHERE lower(username) = lower(' . $adodb->qstr($this->name) . ') LIMIT 1';
		$adodb->CacheFlush($query);

		return 1;
	}

	/**
	 * Get a user's scrobbles ordered by time
	 *
	 * @param int $number The number of scrobbles to return
	 * @param int $offset The position of the first scrobble to return
	 * @param int $from Only return scrobbles with time higher than this timestamp
	 * @param int $to Only return scrobbles with time lower than this timestamp
	 * @return array An array of scrobbles with human time
	 */
	function getScrobbles($number, $offset = 0, $from = false, $to = false) {
		$data = Server::getRecentScrobbles($number, $this->uniqueid, $offset, $from, $to);

		if ($data == null) {
			return array();
		}

		return $data;
	}

	/**
	 * Retrieve a user's avatar via the gravatar service
	 *
	 * @param int $size The desired size of the avatar (between 1 and 512 pixels)
	 * @return string A URL to the user's avatar image
	 */
	function getAvatar($size = 64) {
		if (!empty($this->avatar_uri)) {
			return $this->avatar_uri;
		}

		return 'http://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '?s=' . $size . '&d=monsterid';
	}

	/**
	 * Retrieves the URL to the user's page
	 *
	 * @param string $component Type of user page, profile|stats|recent-tracks|station
	 * @return string URL to the user's page
	 */
	function getURL($component = 'profile', $params = false) {
		return Server::getUserURL($this->name, $component, $params);
	}

	/**
	 * Get a user's now-playing tracks
	 *
	 * @param int $number Amount of tracks to retrieve
	 * @return array An array of nowplaying data or null in case of failure
	 */
	function getNowPlaying($number) {
		return Server::getNowPlaying($number, $this->name);
	}

	/**
	 * Log in to the gnukebox server
	 *
	 * @return array A string containing the session key to be used for scrobbling
	 */
	function getScrobbleSession() {
		return Server::getScrobbleSession($this->uniqueid);
	}

	/**
	 * Log in to the radio server
	 *
	 * @param string $station The station to be played
	 * @return array A string containing the session key to be used for streaming
	 */
	function getRadioSession($station) {
		return Server::getRadioSession($station, $this->name);
	}

	/**
	 * Log in to the web services
	 *
	 * @return array A string containing the web session key
	 */
	function getWebServiceSession() {
		return Server::getWebServiceSession($this->name);
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
		return Server::getTopArtists($limit, $offset, $streamable, $begin, $end, $this->uniqueid, $cache);
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
		return Server::getTopTracks($limit, $offset, $streamable, $begin, $end, null, $this->uniqueid, $cache);
	}

	/**
	 * Get this user's total number of tracks scrobbled
	 *
	 * @param int $since Timestamp to start counting tracks from
	 * @return int Number of tracks scrobbled
	 */
	public function getTotalTracks($since = null) {
		global $adodb;

		if ($since) {
			$query = 'SELECT COUNT(*) FROM Scrobbles WHERE userid = ' . ($this->uniqueid) . ' AND time > ' . (int)($since);
		} else {
			$query = 'SELECT scrobble_count FROM User_Stats WHERE userid = ' . ($this->uniqueid);
		}
		try {
			$tracks = $adodb->CacheGetOne(200, $query);
		} catch (Exception $e) {
			$tracks = 0;
		}

		return $tracks;
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
		return Tag::_getTagData($cache, $limit, $offset, $this->uniqueid);
	}

	/**
	 * Get artists, albums, or tracks tagged with tag by user
	 *
	 * @param string $tag Items are tagged by this tag
	 * @param string $taggingtype Type of tags to return (artist|album|track)
	 * @param int $limit The number of items to return (default is 10)
	 * @param int $offset The position of the first item to return (default is 0)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @param bool $streamable Show only content by streamable artists (default is False)
	 * @return array Item details ((artist, .. , freq) .. )
	 */

	function getPersonalTags($tag, $taggingtype, $limit=10, $offset=0, $cache=600, $streamable=False) {
		if(isset($tag) and isset($taggingtype)) {
			return Tag::_getTagData($cache, $limit, $offset, $this->uniqueid, null, null, null, $tag, $taggingtype, $streamable);
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
			$artistobj = new Artist($artist);
			return $artistobj->getTags($this->uniqueid, $limit, $offset, $cache);
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
			return Tag::_getTagData($cache, 1, 0, $this->uniqueid, null, null, null, $tag);
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
	 * @return array Track details ((artist, track, time, freq, artisturl, trackurl) ..) or empty array in case of failure
	 */
	function getLovedTracks($limit = 20, $offset = 0, $streamable = False, $artist = null, $cache = 600) {
		return Server::getLovedTracks($limit, $offset, $streamable, $artist, $this->uniqueid, $cache);
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
		return Server::getLovedArtists($limit, $offset, $streamable, $this->uniqueid, $cache);
	}

	/**
	 * Retrieves a list of user's banned tracks
	 *
	 * @todo Rewrite this function like $user->getLovedTracks()
	 *
	 * @param int $limit The number of tracks to return
	 * @param int $offset Skip this number of rows before returning tracks
	 * @return array Track details ((userid, track, artist, time) ..) or empty array in case of failure
	 */
	function getBannedTracks($limit = 50, $offset = 0) {
		global $adodb;

		$res = $adodb->CacheGetAll(600, 'SELECT * FROM Banned_Tracks WHERE '
			. ' userid = ' . $this->uniqueid . ' ORDER BY time DESC'
			. ' LIMIT ' . $limit . ' OFFSET ' . $offset);

		return $res;
	}


	/**
	 * Get details of any connections this user has setup to other services.
	 *
	 * @return array An array of service connection details
	 */
	function getConnections() {
		global $adodb;

		$res = $adodb->GetAll('SELECT * FROM Service_Connections WHERE '
			. ' userid = ' . $this->uniqueid);

		return $res;
	}

	/**
	 * Get artists recommended for this user
	 *
	 * @param int $limit The number of artists to return (defaults to 10)
	 * @param bool $random Pick artists at random
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
	 * @param string $artist The name of the artist to check
	 * @return bool Boolean indicating whether this user can edit the artist or not.
	 */
	function manages($artist) {
		global $adodb;

		if ($this->userlevel >= 2) {
			// Let admins edit all artists
			return true;
		}

		$res = $adodb->GetOne('SELECT COUNT(*) FROM Manages WHERE '
			. ' userid = ' . $this->uniqueid
			. ' AND artist = ' . $adodb->qstr($artist)
			. ' AND authorised = 1');

		return $res != 0;
	}

	/**
	 * Checks whether this user has any loved tracks
	 *
	 * @return bool Boolean indicating whether this user has marked any tracks as being loved in the past.
	 */
	function hasLoved() {
		global $adodb;

		$res = $adodb->GetOne('SELECT COUNT(*) FROM Loved_Tracks WHERE '
			. ' userid = ' . $this->uniqueid);

		return $res != 0;
	}

	/**
	 * Find the neighbours of this user based on the number of loved artists shared between them and other users.
	 *
	 * @param int The number of neighbours to return (defaults to 10).
	 * @return array An array of userids, User objects and the number of loved artists shared with this user.
	 */
	function getNeighbours($limit=10) {
		global $adodb;
		if (!$this->hasLoved()) {
			return array();
		}

		$res = $adodb->CacheGetAll(7200, 'SELECT Loved_Tracks.userid AS userid, count(Loved_Tracks.userid) AS shared_artists FROM Loved_Tracks INNER JOIN (SELECT DISTINCT(artist) AS artist FROM Loved_Tracks WHERE userid=' . $this->uniqueid . ') AS Loved_Artists ON Loved_Tracks.artist = Loved_Artists.artist WHERE userid != ' . $this->uniqueid . ' GROUP BY Loved_Tracks.userid ORDER BY shared_artists DESC LIMIT ' . $limit);

		foreach ($res as &$neighbour) {
			$neighbour['user'] = User::new_from_uniqueid_number($neighbour['userid']);
		}

		return $res;
	}

}
