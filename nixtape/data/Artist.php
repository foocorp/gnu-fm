<?php

/* Libre.fm -- a free network service for sharing your music listening habits

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
require_once($install_path . '/data/Album.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . '/data/Server.php');
require_once($install_path . '/utils/linkeddata.php');

/**
 * Represents artist data
 *
 * General artist attributes are accessible as public variables. 
 * Lists of tracks and albums are only generated when requested.
 */
class Artist {


	public $name, $mbid, $streamable, $bio_content, $bio_published, $bio_summary, $image_small, $image_medium, $image_large;
	public $id;

	/**
	 * Artist constructor
	 *
	 * @param string $name The name of the artist to load
	 * @param string $mbid The mbid of the artist (optional)
	 */
	function __construct($name, $mbid=false) {
		global $mdb2;

		$res = $mdb2->query('SELECT name, mbid, streamable, bio_published, bio_content, bio_summary, image_small, image_medium, image_large FROM Artist WHERE '
			. 'mbid = ' . $mdb2->quote($mbid, 'text') . ' OR '
			. 'name = ' . $mdb2->quote($name, 'text'));
		if(!$res->numRows()) {
			return(new PEAR_Error('No such artist: ' . $name));
		} else {
			$row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
			$this->name = $row['name'];
			$this->mbid = $row['mbid'];
			$this->streamable = $row['streamable'];
			$this->bio_published = $row['bio_published'];
			$this->bio_content = $row['bio_content'];
			$this->bio_summary = $row['bio_summary'];
			$this->image_small = $row['image_small'];
			$this->image_medium = $row['image_medium'];
			$this->image_large = $row['image_large'];

			$this->id = identifierArtist(null, $this->name, null, null, null, null, $this->mbid, null);
		}
	}

	/**
	 * Retrieves all an artist's albums
	 *
	 * @return An array of Album objects
	 */
	function getAlbums() {
		global $mdb2;
		$res = $mdb2->query('SELECT name, image FROM Album WHERE artist_name = '
			. $mdb2->quote($this->name, 'text'));
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$albums[] = new Album($row['name'], $this->name);
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
		$res = $mdb2->query('SELECT name FROM Track WHERE artist_name = '
			. $mdb2->quote($this->name, 'text'));
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$tracks[] = new Track($row['name'], $this->name);
		}

		return $tracks;
	}

	/**
	 * Retrieves an artist's most popular tracks
	 *
	 * @param int $tracks the number of tracks to return
	 * @return An array of Track objects
	 */
	function getTopTracks($number) {
		global $mdb2;
		$res = $mdb2->query('SELECT track, COUNT(track) AS freq, COUNT(DISTINCT username) AS listeners FROM Scrobbles WHERE'
			. ' artist = ' . $mdb2->quote($this->name, 'text')
			. ' GROUP BY track ORDER BY freq DESC LIMIT ' . $mdb2->quote($number, 'integer'));
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$track = new Track($row['track'], $this->name);
			$track->setPlayCount($row['freq']);
			$track->setListenerCount($row['listeners']);
			$tracks[] = $track;
		}

		return $tracks;
	}

	/**
	 * Gives the URL for this artist
	 *
	 * @return A string containing the URL of this artist
	 */
	function getURL() {
		Server::getArtistURL($this->name);
	}

}
