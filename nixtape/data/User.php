<?php

/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

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
	public $password;

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
			global $mdb2;
			$res = $mdb2->query('SELECT * FROM Users WHERE lower(username) = ' . $mdb2->quote(strtolower($name), 'text'));
			if($res->numRows()) {
				$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
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
			$this->id           = $row["webid_uri"];
			$this->webid_uri    = $row["webid_uri"];
			$this->avatar_uri   = $row["avatar_uri"];
			$this->laconica_profile = $row["laconica_profile"];
			$this->journal_rss  = $row["journal_rss"];
			$this->acctid       = $this->getURL() . '#acct';
			
			if (! preg_match('/\:/', $this->id))
				$this->id = $this->getURL() . '#me';
		}		
	}
	
	function save ()
	{
		global $mdb2;
		
		// It appears we just discard this data, but this is here for a reason!
		// getLocationDetails will fill in latitude,longitude details into the Places table in the database
		// if it's not already there. This is important as the location_uri field is a foreign key.
		if (!empty($this->location_uri))
			$dummy = Server::getLocationDetails($this->location_uri);
		
		$q = sprintf("UPDATE Users SET "
				. "email=%s, "     # Send a confirmation email first??
				. "password=%s, "
				. "fullname=%s, "
				. "homepage=%s, "
				. "bio=%s, "
				. "location=%s, "
				. "userlevel=%d, "
				. "webid_uri=%s, "
				. "location_uri=%s, "
				. "avatar_uri=%s, "
				. "laconica_profile=%s, "
				. "journal_rss=%s, "
				. "modified=%d "
				. "WHERE username=%s"
				, $mdb2->quote($this->email, 'text')
				, $mdb2->quote($this->password, 'text')
				, $mdb2->quote($this->fullname, 'text')
				, $mdb2->quote($this->homepage, 'text')
				, $mdb2->quote($this->bio, 'text')
				, $mdb2->quote($this->location, 'text')
				, $this->userlevel
				, $mdb2->quote($this->id, 'text')
				, (empty($this->location_uri) ? 'NULL' : $mdb2->quote($this->location_uri, 'text'))
				, $mdb2->quote($this->avatar_uri, 'text')
				, $mdb2->quote($this->laconica_profile, 'text')
				, $mdb2->quote($this->journal_rss, 'text')
				, time()
				, $mdb2->quote($this->name, 'text'));
				
		$res = $mdb2->query($q);
		
		if(PEAR::isError($res)) {
			header("Content-Type: text/plain");
			print_r($res);
			exit;
		}

		return 1;
	}

	/**
	 * Get a user's scrobbles ordered by time
	 *
	 * @param int $number The number of scrobbles to return
	 * @return An array of scrobbles with human time
	 */
	function getScrobbles($number) {
		$data = Server::getRecentScrobbles($number, $this->name);
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
	
		return "http://www.gravatar.com/avatar/" . md5($this->email) . "?s=" . $size . "&d=monsterid";
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
		global $mdb2;
		$session_id = md5(mt_rand() . time());
		$sql = "INSERT INTO Scrobble_Sessions(username, sessionid, client, expires) VALUES ("
			. $mdb2->quote($this->name, "text") . ","
			. $mdb2->quote($session_id, "text") . ","
			. "'lfm',"
			. $mdb2->quote(time() + 86400) . ")";
		$mdb2->query($sql);
		return $session_id;
	}

	/**
	 * get user's top 20 tracks
	 *
	 * @return user's top 20 tracks
	 */
	function getTopTracks($number=20) {
		global $mdb2;

		$res = $mdb2->query("SELECT COUNT(track) as c, artist, album, track FROM Scrobbles WHERE rating<>'S' AND username=".$mdb2->quote($this->name,"text")." GROUP BY artist,album,track ORDER BY c DESC LIMIT 20");

	        if(PEAR::isError($res)) {
	            return $res;
	        }

		$maxcount = 0;

	        $data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
	        foreach($data as $i) {
	            $row = sanitize($i);
	            $row["artisturl"] = Server::getArtistURL($row["artist"]);
	            $row["trackurl"] = Server::getTrackURL($row["artist"],$row["album"],$row["track"]);
		    if ((int)$row["c"] > $maxcount) {
			$maxcount = (int)$row["c"];
		    }
	            $result[] = $row;
	        }

		if ($maxcount > 0) {
			foreach($result as &$row) {
			$row["width"]=(int)(100 * ($row["c"]/$maxcount));
			}
		}

	        return $result;
	}


}

