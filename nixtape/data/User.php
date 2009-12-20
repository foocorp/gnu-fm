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

/**
 * Represents User data
 *
 * General attributes are accessible as public variables.
 *
 */
class User {

	public $name, $email, $fullname, $bio, $location, $homepage, $error, $userlevel;
	public $id, $acctid, $avatar_uri, $location_uri, $webid_uri, $laconica_profile, $journal_rss;
	public $password, $has_identica, $created, $modified, $uniqueid, $anticommercial;

	/**
	 * User constructor
	 *
	 * @param string $name The name of the user to load
	 */
	function __construct($name, $data=null) {

		global $base_url;
		$base = preg_replace('#/$#', '', $base_url);

		if (is_array($data)) {
			$row = $data;
		}
		else {
			global $adodb;
			$query = 'SELECT * FROM Users WHERE lower(username) = ' . $adodb->qstr(strtolower($name)) . ' LIMIT 1';
			$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	                $row = $adodb->CacheGetRow(7200,$query);
			if (!$row) {
				throw new Exception('EUSER', 22);
	                }
		}

		if (is_array($row)) {
			$this->name         = $row['username'];
			$this->password     = $row['password'];
			$this->email	    = $row['email'];
			$this->fullname     = $row['fullname'];
			$this->homepage     = $row['homepage'];
			$this->bio	    = $row['bio'];
			$this->location     = $row['location'];
			$this->location_uri = $row['location_uri'];
			$this->userlevel    = $row['userlevel'];
			$this->id           = $row['webid_uri'];
			$this->webid_uri    = $row['webid_uri'];
			$this->avatar_uri   = $row['avatar_uri'];
			$this->laconica_profile = $row['laconica_profile'];
			$this->journal_rss  = $row['journal_rss'];
			$this->acctid       = $this->getURL() . '#acct';
			$this->created	    = $row['created'];
			$this->modified     = $row['modified'];
			$this->uniqueid     = $row['uniqueid'];
			$this->anticommercial     = $row['anticommercial'];

			$this->has_identica = preg_match('#^http://identi\.ca/#i', $this->laconica_profile);

			if (! preg_match('/\:/', $this->id))
				$this->id = $this->getURL() . '#me';
		}
	}


	public static function new_from_uniqueid_number ($uid)
	{
		global $adodb;
		$query = sprintf('SELECT * FROM Users WHERE uniqueid = %d', (int)$uid);
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$row = $adodb->CacheGetRow(7200,$query);

		if($row) {
			return new User($row['username'], $row);
		} else {
			return false;
		}
	}

	function save ()
	{
		global $adodb;

		// It appears we just discard this data, but this is here for a reason!
		// getLocationDetails will fill in latitude,longitude details into the Places table in the database
		// if it's not already there. This is important as the location_uri field is a foreign key.
		if (!empty($this->location_uri))
			$dummy = Server::getLocationDetails($this->location_uri);

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
				, time()
				, $adodb->qstr($this->name));

		try {
			$res = $adodb->Execute($q);
		} catch (exception $e) {
			header('Content-Type: text/plain');
			exit;
		}

		$query = 'SELECT * FROM Users WHERE lower(username) = ' . $adodb->qstr(strtolower($this->name)) . ' LIMIT 1';
		$adodb->CacheFlush($query);

		return 1;
	}

	/**
	 * Get a user's scrobbles ordered by time
	 *
	 * @param int $number The number of scrobbles to return
	 * @return An array of scrobbles with human time
	 */
	function getScrobbles($number) {
		try {
			$data = Server::getRecentScrobbles($number, $this->uniqueid);
		} catch (exception $e) {
			throw (new Exception('Breakage while getting recent scrobbles'));
		}
		if(!isset($data)) { return array(); }
		return $data;
	}

	/**
	 * Retrieve a user's avatar via the gravatar service
	 *
	 * @param int $size The desired size of the avatar (between 1 and 512 pixels)
	 * @return A URL to the user's avatar image
	 */
	function getAvatar($size=64) {
		if (!empty($this->avatar_uri))
			return $this->avatar_uri;

		return 'http://www.gravatar.com/avatar/' . md5($this->email) . '?s=' . $size . '&d=monsterid';
	}

	function getURL($component='profile') {
		return Server::getUserURL($this->name, $component);
	}

	/**
	 * Get a user's now-playing tracks
	 *
	 * @return An array of nowplaying data
	 */
	function getNowPlaying($number) {
		return Server::getNowPlaying($number, $this->name);
	}

	/**
	 * Log in to the gnukebox server
	 *
	 * @return A string containing the session key to be used for scrobbling
	 */
	function getScrobbleSession() {
		global $adodb;
		$session_id = md5(mt_rand() . time());
		$sql = 'INSERT INTO Scrobble_Sessions(userid, sessionid, client, expires) VALUES ('
			. ($this->uniqueid) . ','
			. $adodb->qstr($session_id) . ','
			. '\'lfm\','
			. (time() + 86400) . ')';
		if($adodb->Execute($sql)) {
			return $session_id;
		} else {
			return false;
		}
	}

	/**
	 * Log in to the radio server
	 *
	 * @param string $station The station to be played
	 * @return A string containing the session key to be used for streaming
	 */
	function getRadioSession($station) {
		return Server::getRadioSession($station, $this->name);
	}

	/**
	 * get user's top 20 tracks
	 *
	 * @return user's top 20 tracks
	 */
	function getTopTracks($number=20, $since=null) {
		global $adodb;

		if ($since) {
			$query = 'SELECT COUNT(track) as freq, artist, album, track FROM Scrobbles WHERE userid = '.($this->uniqueid).' AND time > '.(int)($since).' GROUP BY artist,album,track ORDER BY freq DESC LIMIT ' . ($number);
		} else {
			$query = 'SELECT COUNT(track) as freq, artist, album, track FROM Scrobbles WHERE userid = '.($this->uniqueid).' GROUP BY artist,album,track ORDER BY freq DESC LIMIT ' . ($number);
		}
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll(7200,$query);
		if (!$data) {
			throw new Exception('ERROR ' . $query);
		}

		$maxcount = 0;

		foreach($data as &$i) {
			$row = sanitize($i);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'],$row['album'],$row['track']);
			if ((int)$row['freq'] > $maxcount) {
				$maxcount = (int)$row['freq'];
			}
			$result[] = $row;
		}

		if ($maxcount > 0) {
			foreach($result as &$row) {
				$row['width']=(int)(100 * ($row['freq']/$maxcount));
			}
		}

		return $result;
	}

	public function getTotalTracks($since=null) {
		global $adodb;

		if ($since) {
			$query = 'SELECT COUNT(*) FROM Scrobbles WHERE userid = '.($this->uniqueid).' AND time > '.(int)($since);
		} else {
			$query = 'SELECT COUNT(*) FROM Scrobbles WHERE userid = '.($this->uniqueid);
		}
		try {
			$tracks = $adodb->CacheGetOne(200, $query);
		} catch (exception $e) {
			$tracks = 0;
		}

		return $tracks;
	}
}
