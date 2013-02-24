<?php
/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2013 Free Software Foundation, Inc

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

require_once($install_path . '/data/Library.php');
require_once('xml.php');

/**
 * Class with functions that returns XML-formatted data involving a user's library.
 *
 * These functions are mainly used by web service methods.
 *
 * @package API
 */
class LibraryXML {

	public static function removeScrobble($userid, $timestamp, $artist, $track) {
		$result = Library::removeScrobble($userid, $timestamp, $artist, $track);

		if (!$result) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		return $xml;
	}

}
