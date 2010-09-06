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
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Album.php');
require_once($install_path . '/data/Server.php');
require_once($install_path . '/utils/resolve-external.php');
require_once($install_path . '/utils/licenses.php');
require_once($install_path . '/utils/linkeddata.php');

/**
 * Represents track data
 *
 * All track attributes are accessible as public variables.
 */
class Track {

	public $name, $artist_name, $album_name, $mbid, $duration, $streamable, $license, $downloadurl, $streamurl;
	public $id;

	private $_playcount = false, $_listenercount = false;

	/**
	 * Track constructor
	 *
	 * @param string $name The name of the track to load
	 * @param string $artist The name of the artist who recorded this track
	 */
	function __construct($name, $artist) {
		global $adodb;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = $adodb->CacheGetRow(600, 'SELECT name, artist_name, album_name, duration, streamable, license, downloadurl, streamurl, mbid FROM Track WHERE '
			. 'lower(name) = ' . strtolower($adodb->qstr($name)) . ' AND '
			. 'lower(artist_name) = ' . strtolower($adodb->qstr($artist)));
		if(!$res) {
			$this->name = 'No such track: ' . $name;
		} else {
			$row = $res;
			$this->name = $row['name'];
			$this->mbid = $row['mbid'];
			$this->artist_name = $row['artist_name'];
			$this->album_name = $row['album_name'];
			$this->duration = $row['duration'];
			$this->streamable = $row['streamable'];
			$this->license = simplify_license($row['license']);
			$this->licenseurl = $row['license'];
			$this->downloadurl = resolve_external_url($row['downloadurl']);
			$this->streamurl = resolve_external_url($row['streamurl']);

			$this->id = identifierTrack(null, $this->artist_name, $this->name, $this->album_name, null, $this->mbid, null, null);
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
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$row = $adodb->CacheGetRow(300, 'SELECT COUNT(track) AS freq, COUNT(DISTINCT userid) AS listeners FROM Scrobbles WHERE'
			. ' artist = ' . $adodb->qstr($this->artist_name)
			. ' AND track = ' . $adodb->qstr($this->name)
			. ' GROUP BY track ORDER BY freq DESC');

		if (!isset($row)) {
		        $this->setPlaycount(0);
			$this->setListenerCount(0);
		} else {
			$this->setPlaycount($row['freq']);
			$this->setListenerCount($row['listeners']);
		}
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

	/**
	 * Retrieve the tags for this track.
	 *
	 * @return An array of tag names and how frequent they are
	 */
	function getTopTags() {
		global $adodb;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

		$res = $adodb->CacheGetAll(600, 'SELECT COUNT(tag) AS freq, tag FROM tags WHERE'
			. ' artist = ' . $adodb->qstr($this->artist_name)
			. ' AND track = ' . $adodb->qstr($this->name)
			. ' GROUP BY tag ORDER BY freq DESC');
		
		return $res;
	}

	/**
	 * Retrieve a specific user's tags for this track.
	 *
	 * @return An array of tag names.
	 */
	function getTags($userid) {
		global $adodb;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

		$res = $adodb->GetAll('SELECT tag FROM tags WHERE'
			. ' artist = ' . $adodb->qstr($this->artist_name)
			. ' AND track = ' . $adodb->qstr($this->name)
			. ' AND userid = ' . $userid);

		$tags = array();
		foreach($res as &$row) {
			$tags[] = $row['tag'];
		}

		return $tags;
	}

}
