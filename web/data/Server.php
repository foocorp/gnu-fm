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
	static function getRecentScrobbles($number=10, $username=false) {
		global $mdb2;

		if($username) {
			$res = $mdb2->query('SELECT username, artist, track, time FROM Scrobbles WHERE username = ' . $mdb2->quote($username, "text") . ' ORDER BY time DESC LIMIT ' . $mdb2->quote($number, "integer"));
		} else {
			$res = $mdb2->query('SELECT username, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT ' . $mdb2->quote($number, "integer"));
		}

		if(PEAR::isError($res)) {
			return $res;
		}

		$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
		foreach($data as $i) {
			$row = sanitize($i);
			$row["userurl"] = Server::getUserURL($row["username"]);
			$row["artisturl"] = Server::getArtistURL($row["artist"]);
			$result[] = $row;
		}

		return $result;
	}

    /**
     * Retrieves a list of popular artists
     *
     * @param int $number The number of artists to return
     * @return An array of artists or a PEAR_Error in case of failure
     */
    static function getTopArtists($number=20) {
        global $mdb2;

        $res = $mdb2->query("SELECT COUNT(artist) as c, artist FROM Scrobbles GROUP BY artist ORDER BY c DESC LIMIT 0,20");

        if(PEAR::isError($res)) {
            return $res;
        }

        $data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
        foreach($data as $i) {
            $row = sanitize($i);
            $row["artisturl"] = Server::getArtistURL($row["artist"]);
            $result[] = $row;
        }

        return $result;
    }

	/**
	 * Retrieves a list of the currently playing tracks
	 *
	 * @param int $number The maximum number of tracks to return
	 * @return An array of now playing data or a PEAR_Error in case of failure
	 */
	static function getNowPlaying($number, $username=false) {
		global $mdb2;

		if($username) {
			$res = $mdb2->query('SELECT username, artist, track, client, ClientCodes.name, ClientCodes.url, Now_Playing.mbid from Now_Playing LEFT OUTER JOIN Scrobble_Sessions ON Now_Playing.sessionid=Scrobble_Sessions.sessionid LEFT OUTER JOIN ClientCodes ON Scrobble_Sessions.client=ClientCodes.code WHERE username = ' . $mdb2->quote($username, "text") . ' ORDER BY Now_Playing.expires DESC LIMIT ' . $mdb2->quote($number, "integer"));
		} else {
			$res = $mdb2->query('SELECT username, artist, track, client, ClientCodes.name, ClientCodes.url, Now_Playing.mbid from Now_Playing LEFT OUTER JOIN Scrobble_Sessions ON Now_Playing.sessionid=Scrobble_Sessions.sessionid LEFT OUTER JOIN ClientCodes ON Scrobble_Sessions.client=ClientCodes.code ORDER BY Now_Playing.expires DESC LIMIT ' . $mdb2->quote($number, "integer"));
		}

		if(PEAR::isError($res)) {
			return $res;
		}

		$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
		foreach($data as &$i) {
			$row = sanitize($i);
			if($row["name"] == "") {
				$clientstr = strip_tags(stripslashes($row["client"])) . " (unknown, please tell us what this is)";
			} else {
				$clientstr = "<a href=\"" . strip_tags(stripslashes($row["url"])) . "\">" . strip_tags(stripslashes($row["name"])) . "</a>";
			}
			$row["clientstr"] = $clientstr;
			$row["userurl"] = Server::getUserURL($row["username"]);
			$row["artisturl"] = Server::getArtistURL($row["artist"]);
			$result[] = $row;
		}

		return $result;
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

	static function getArtistURL($artist) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . "/artist/" . urlencode(stripslashes($artist));
		} else {
			return $base_url . "/artist.php?artist=" . urlencode(stripslashes($artist));
		}
	}

    static function getAlbumURL($artist, $album) {
        global $friendly_urls, $base_url;
        $artist_encoded = urlencode(stripslashes($artist));
        $album_encoded = urlencode(stripslashes($album));
		if($friendly_urls) {
			return $base_url . "/artist/" . $artist_encoded . "/album/" . $album_encoded;
		} else {
			return $base_url . "/artist.php?artist=" . $artist_encoded . "&album=" . $album_encoded;
		}
	}
}
