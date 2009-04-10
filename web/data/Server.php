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
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . '/data/User.php');
require_once($install_path . "/data/sanitize.php");

/**
 * Provides access to server-wide data
 *
 * All methods are statically accessible
 */
class Server {

	/**
	 * Retrieves a list of recent scrobbles
	 *
	 * @param int $number The number of scrobbles to return
	 * @return An array of scrobbles or a PEAR_Error in case of failure
	 */
	static function getRecentScrobbles($number) {
		global $mdb2;
		
		$res = $mdb2->query('SELECT username, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 10');

		if(PEAR::isError($res)) {
			return $res;
		}

		$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
		foreach($data as $i) {
			$i = sanitize($i);
			$i["userurl"] = Server::getUserURL($i["username"]);
		}

		return $data;
	}

	/**
	 * Retrieves a list of the currently playing tracks
	 *
	 * @param int $number The maximum number of tracks to return
	 * @return An array of now playing data or a PEAR_Error in case of failure
	 */
	static function getNowPlaying($number) {
		global $mdb2;

		$res = $mdb2->query('SELECT username, artist, track, client, ClientCodes.name, ClientCodes.url from Now_Playing LEFT OUTER JOIN Scrobble_Sessions ON Now_Playing.sessionid=Scrobble_Sessions.sessionid LEFT OUTER JOIN ClientCodes ON Scrobble_Sessions.client=ClientCodes.code ORDER BY Now_Playing.expires DESC LIMIT ' . $mdb2->quote($number, "integer"));

		if(PEAR::isError($res)) {
			return $res;
		}

		$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
		foreach($data as &$i) {
			$i = sanitize($i);
			if($i["name"] == "") {
				$clientstr = strip_tags(stripslashes($i["client"])) . " (unknown, please tell us what this is)";
			} else {
				$clientstr = "<a href=\"" . strip_tags(stripslashes($i["url"])) . "\">" . strip_tags(stripslashes($i["name"])) . "</a>";
			}
			$i["clientstr"] = $clientstr;
			$i["userurl"] = Server::getUserURL($i["username"]);
		}

		return $data;
	}

	/**
	 * The get*URL functions are implemented here rather than in their respective
	 * objects so that we can produce URLs without needing to build whole objects.
	 *
	 * @param string $username The username we want a URL for
	 * @return A string containing URL to the user's profile
	 */
	static function getUserURL($username) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . "/user/" . urlencode(stripslashes($username));
		} else {
			return $base_url . "/profile.php?user=" . urlencode(stripslashes($username));
		}
	}

}
