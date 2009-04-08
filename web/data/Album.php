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

/**
 * Represents album data
 *
 * General album attributes are accessible as public variables. 
 * Lists of tracks are only generated when requested.
 */
class Album {


	/**
	 * Album constructor
	 *
	 * @param string name The name of the album to load
	 * @param string artist The name of the artist who recorded this album
	 */
	function __construct($name, $artist) {
	}

	/**
	 * Retrieves all the tracks in an album
	 *
	 * @return An array of Track objects
	 */
	function getTracks() {
	}

}
