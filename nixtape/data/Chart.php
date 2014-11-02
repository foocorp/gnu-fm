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
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Server.php');

/**
 * Represents chart data
 */
class Chart {

	/**
	 * Get the site's top artists
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
		return Server::getTopArtists($limit, $offset, $streamable, $begin, $end, null, $cache);
	}
}
