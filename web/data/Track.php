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

/**
 * Represents track data
 *
 * All track attributes are accessible as public variables. 
 */
class Track {

	public $name, $artist, $album, $mbid, $duration, $streamable, $license, $downloadurl;

	/**
	 * Track constructor
	 *
	 * @param string name The name of the track to load
	 * @param string artist The name of the artist who recorded this track
	 */
	function __construct($name, $artist) {
		global $mdb2;
		$res = $mdb2->query("SELECT name, artist, album, duration, streamable, license, downloadurl, mbid FROM Track WHERE "
			. "name = " . $mdb2->quote($name, "text") . " AND "
			. "artist = " . $mdb2->quote($artist, "text"));
		if(!$res->numRows()) {
			$this->name = "No such track.";
		} else {
			$row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
			$this->name = $row["name"];
			$this->mbid = $row["mbid"];
			$this->artist = new Artist($row["artist"]);
			$this->album = new Album($row["album"], $row["artist"]);
			$this->duration = $row["duration"];
			$this->streamable = $row["streamable"];
			$this->license = $row["license"];
			$this->downloadurl = $row["downloadurl"];
		}

	}


}
