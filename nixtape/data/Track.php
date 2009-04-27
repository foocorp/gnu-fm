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
require_once($install_path . "/data/Artist.php");
require_once($install_path . "/data/Album.php");
require_once($install_path . "/data/Server.php");
require_once($install_path . "/resolve-external.php");
require_once($install_path . "/licenses.php");

/**
 * Represents track data
 *
 * All track attributes are accessible as public variables. 
 */
class Track {

	public $name, $artist_name, $album_name, $mbid, $duration, $streamable, $license, $downloadurl, $streamurl;

	private $_playcount = false, $_listenercount = false;

	/**
	 * Track constructor
	 *
	 * @param string $name The name of the track to load
	 * @param string $artist The name of the artist who recorded this track
	 */
	function __construct($name, $artist) {
		global $mdb2;
		$res = $mdb2->query("SELECT name, artist, album, duration, streamable, license, downloadurl, streamurl, mbid FROM Track WHERE "
			. "name = " . $mdb2->quote($name, "text") . " AND "
			. "artist = " . $mdb2->quote($artist, "text"));
		if(!$res->numRows()) {
			$this->name = "No such track: " . $name;
		} else {
			print "<!-- TRACK: ".$row["album"]. " -->";
			$row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
			print "<!-- AFTERTRACK: ".$row["album"]. " -->";
			$this->name = $row["name"];
			$this->mbid = $row["mbid"];
			$this->artist_name = $row["artist"];
			$this->album_name = $row["album"];
			$this->duration = $row["duration"];
			$this->streamable = $row["streamable"];
			$this->license = simplify_license($row["license"]);
			$this->licenseurl = $row["license"];
			$this->downloadurl = resolve_external_url($row["downloadurl"]);
			$this->streamurl = resolve_external_url($row["streamurl"]);
		}

	}

	/**
	 * Sets the playcount
	 *
	 * @param int $playcount The number of plays this track has received
	 */
	function setPlayCount($playcount) {
		$this->_playcount = $playcount;
	}

	/**
	 * Sets the number of listeners
	 *
	 * @param int $listeners The number of people who've listened to this track
	 */
	function setListenerCount($listeners) {
		$this->_listenercount = $listeners;
	}

	/**
	 *
	 * @return An int indicating the number of times this track has been played
	 */
	function getPlayCount() {
		if($this->_playcount) {
			// If we've been given a cached value from another SQL call use that
			return $this->_playcount;
		}

		$this->_getPlayCountAndListenerCount();
		return $this->_playcount;
	}

	/**
	 *
	 * @return An int indicating the number of listeners this track has
	 */
	function getListenerCount() {
		if($this->_listeners) {
			return $this->_listenercount;
		}

		$this->_getPlayCountAndListenerCount();
		return $this->_listenercount;
	}


	private function _getPlayCountAndListenerCount() {
		global $mdb2;

		$res = $mdb2->query("SELECT COUNT(track) AS freq, COUNT(DISTINCT username) AS listeners FROM Scrobbles WHERE rating<>'S' AND "
			. " artist = " . $mdb2->quote($this->artist_name, 'text') 
			. " AND track = " . $mdb2->quote($this->name, "text")
			. " GROUP BY track ORDER BY freq DESC");

		$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$this->_playcount = $row['freq'];
		$this->_listenercount = $row['listeners'];
	}

	/**
	 * Retrieve the artist for this track.
	 *
	 * @return An artist object
	 */
	function getArtist() {
		return new Artist($this->artist_name);
	}

	function getURL() {
		return Server::getTrackURL($this->artist_name, $this->album_name, $this->name);
	}

}
