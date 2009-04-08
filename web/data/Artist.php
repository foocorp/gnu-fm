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
require_once($install_path . "/data/sanitize.php");

/**
 * Represents artist data
 *
 * General artist attributes are accessible as public variables. 
 * Lists of tracks and albums are only generated when requested.
 */
class Artist {


	public $name, $mbid, $streamable, $bio_content, $bio_published, $bio_summary, $image_small, $image_medium, $image_large;

	/**
	 * Artist constructor
	 *
	 * @param string name The name of the artist to load
	 */
	function __construct($name) {
		global $mdb2;
		$res = $mdb2->query("SELECT name, mbid, streamable, bio_published, bio_content, bio_summary, image_small, image_medium, image_large FROM Artist WHERE "
			. "name = " . $mdb2->quote($name, "text"));
		if(!$res->numRows()) {
			$this->artist_name = "No such artist.";
			$this->bio_summary = "Sorry we don't have a record of this artist in our database.";
		} else {
			$row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
			$this->name = $row["name"];
			$this->mbid = $row["mbid"];
			$this->streamable = $row["streamable"];
			$this->bio_published = $row["bio_published"];
			$this->bio_content = $row["bio_content"];
			$this->bio_summary = $row["bio_summary"];
			$this->image_small = $row["image_small"];
			$this->image_medium = $row["image_medium"];
			$this->image_large = $row["image_large"];
		}
	}

	/**
	 * Retrieves all an artist's albums
	 *
	 * @return An array of Album objects
	 */
	function getAlbums() {
		global $mdb2;
		$res = $mdb2->query("SELECT name FROM Album WHERE artist_name = "
			. $mdb2->quote($this->name, "text"));
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$albums[] = new Album($res["name"], $res["artist"]);
		}

		return $albums;
	}

	/**
	 * Retrieves all an artist's tracks
	 *
	 * @return An array of Track objects
	 */
	function getTracks() {
		global $mdb2;
		$res = $mdb2->query("SELECT name, artist FROM Track WHERE artist = "
			. $mdb2->quote($this->name, "text"));
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$tracks[] = new Track($res["name"], $res["artist"]);
		}

		return $tracks;
	}



}
