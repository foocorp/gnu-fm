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
require_once($install_path . '/utils/linkeddata.php');
require_once($install_path . "/resolve-external.php");
require_once($install_path . '/licenses.php'); // why isn't this in a subdir?

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
			$res = $mdb2->query(
				"SELECT
					s.username, 
					s.artist, 
					s.track, 
					s.album, 
					s.time, 
					s.mbid, 
					a.mbid AS artist_mbid,
					l.mbid AS album_mbid,
					l.image AS album_image,
                                        l.artwork_license,
					t.license
				FROM Scrobbles s 
				LEFT JOIN Artist a
					ON s.artist=a.name
				LEFT JOIN Album l
					ON l.artist_name=s.artist
					AND l.name=s.album
				LEFT JOIN Track t
					ON lower(s.artist) = lower(t.artist)
					AND lower(s.album) = lower(t.album)
					AND lower(s.track) = lower(t.name)
				WHERE s.rating<>'S'
					AND lower(s.username) = " . $mdb2->quote(strtolower($username), "text") . ' 
				ORDER BY
					s.time DESC 
				LIMIT ' . $mdb2->quote($number, "integer"));
		} else {
			$res = $mdb2->query(
				"SELECT
					s.username,
					s.artist, 
					s.track,
					s.album,
					s.time,
					s.mbid,
					a.mbid AS artist_mbid,
					l.mbid AS album_mbid,
					l.image AS album_image,
                                        l.artwork_license,
					t.license
				FROM Scrobbles s
				LEFT JOIN Artist a
					ON s.artist=a.name
				LEFT JOIN Album l
					ON l.artist_name=s.artist
					AND l.name=s.album
				LEFT JOIN Track t
					ON lower(s.artist) = lower(t.artist)
					AND lower(s.album) = lower(t.album)
					AND lower(s.track) = lower(t.name)
				WHERE s.rating<>'S'
				ORDER BY
					s.time DESC 
				LIMIT " . $mdb2->quote($number, "integer"));
		}

		if(PEAR::isError($res)) {
			return $res;
		}

		$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
		foreach($data as $i) {
			$row = sanitize($i);
			
			$row["userurl"] = Server::getUserURL($row["username"]);
			if ($row['album'])
				$row["albumurl"] = Server::getAlbumURL($row["artist"], $row["album"]);
			$row["artisturl"] = Server::getArtistURL($row["artist"]);
			$row["trackurl"] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);

  			$row['timehuman'] = human_timestamp($row['time']);
			$row["timeiso"]   = date('c', (int)$row['time']);
			
			$row['id']        = identifierScrobbleEvent($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_artist'] = identifierArtist($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_track']  = identifierTrack($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_album']  = identifierAlbum($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);

			if (!$row['album_image']) {
				$row['album_image'] = false;
			} else {
				$row['album_image'] = resolve_external_url($row['album_image']);
			}

			if ($row['artwork_license'] == "amazon") {
				$row['album_image'] = str_replace("SL160","SL50",$row['album_image']);
			}

			$row["licenseurl"] = $row["license"];
			$row["license"] = simplify_license($row["licenseurl"]);
			
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

        $res = $mdb2->query("SELECT COUNT(artist) as c, artist FROM Scrobbles WHERE rating<>'S' GROUP BY artist ORDER BY c DESC LIMIT 20");

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
			$res = $mdb2->query("SELECT
						username,
						n.artist,
						n.track,
						n.album,
						client,
						ClientCodes.name,
						ClientCodes.url,
						ClientCodes.free,
						n.mbid,
						t.license
					FROM Now_Playing n
					LEFT OUTER JOIN Scrobble_Sessions
						ON n.sessionid=Scrobble_Sessions.sessionid
					LEFT OUTER JOIN ClientCodes
						ON Scrobble_Sessions.client=ClientCodes.code
					LEFT OUTER JOIN Track t
						ON lower(n.artist) = lower(t.artist)
						AND lower(n.album) = lower(t.album)
						AND lower(n.track) = lower(t.name)
					WHERE lower(username) = " . $mdb2->quote(strtolower($username), "text") . "
					ORDER BY n.expires DESC LIMIT " . $mdb2->quote($number, "integer"));
		} else {
			$res = $mdb2->query("SELECT
						username,
						n.artist,
						n.track,
						n.album,
						client,
						ClientCodes.name,
						ClientCodes.url,
						ClientCodes.free,
						n.mbid,
						t.license
					FROM Now_Playing n
					LEFT OUTER JOIN Scrobble_Sessions
						ON n.sessionid=Scrobble_Sessions.sessionid
					LEFT OUTER JOIN ClientCodes
						ON Scrobble_Sessions.client=ClientCodes.code
					LEFT OUTER JOIN Track t
						ON lower(n.artist) = lower(t.artist)
						AND lower(n.album) = lower(t.album)
						AND lower(n.track) = lower(t.name)
					ORDER BY n.expires DESC LIMIT " . $mdb2->quote($number, "integer"));
		}

		if(PEAR::isError($res)) {
			return $res;
		}

		$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
		foreach($data as &$i) {
			$row = sanitize($i);
			// this logic should be cleaned up and the free/nonfree decision be moved into the smarty templates
			if($row["name"] == "") {
				$clientstr = strip_tags(stripslashes($row["client"])) . " (unknown, <a href=\"http://ideas.libre.fm/index.php/Client_Codes\">please tell us what this is</a>)";
			} elseif($row["free"] == "Y") {
				$clientstr = "<a href=\"" . strip_tags(stripslashes($row["url"])) . "\">" . strip_tags(stripslashes($row["name"])) . "</a>";
			} else {
				$clientstr = "<a href=\"http://en.wikipedia.org/wiki/Category:Free_media_players\">" . strip_tags(stripslashes($row["name"]) . "</a>");
			}
			$row["clientstr"] = $clientstr;
			$row["userurl"] = Server::getUserURL($row["username"]);
			$row["artisturl"] = Server::getArtistURL($row["artist"]);
			$row["trackurl"] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);
			
			// We really want to get an image URI from the database and only fall back to qm50.png if we can't find an image.
			$row['albumart'] = $base_url . '/i/qm50.png';

			$row["licenseurl"] = $row["license"];
			$row["license"] = simplify_license($row["licenseurl"]);
			
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
		if($friendly_urls) {
			return $base_url . "/artist/" . urlencode(stripslashes($artist)) . "/album/" . urlencode(stripslashes($album));
		} else {
			return $base_url . "/album.php?artist=" . urlencode(stripslashes($artist)) . "&album=" . urlencode(stripslashes($album));
		}
	}

	static function getTrackURL($artist, $album, $track) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . "/artist/" . ($artist) . "/album/" . ($album) . "/track/" . ($track);
		} else {
			return $base_url . "/track.php?artist=" . ($artist) .   "&album=" . ($album) . "&track=" . ($track);
		}
	}

}
