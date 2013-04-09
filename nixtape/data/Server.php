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
// require_once($install_path . '/data/Group.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . '/data/User.php');
require_once($install_path . '/data/sanitize.php');
require_once($install_path . '/utils/linkeddata.php');
require_once($install_path . '/utils/arc/ARC2.php');
require_once($install_path . '/utils/resolve-external.php');
require_once($install_path . '/utils/licenses.php');
require_once($install_path . '/utils/rewrite-encode.php');
require_once($install_path . '/temp-utils.php'); // this is extremely dodgy and shameful
require_once($install_path . '/data/clientcodes.php');

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
	 * @param int $userid The user id to return scrobbles for
	 * @param int $offset Amount of entries to skip before returning scrobbles
	 * @return array Scrobbles or null in case of failure
	 */
	static function getRecentScrobbles($number = 10, $userid = false, $offset = 0) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			if ($userid) {
				$res = $adodb->CacheGetAll(60,
					'SELECT Scrobbles.*, Loved_Tracks.userid as loved
					FROM Scrobbles LEFT JOIN Loved_Tracks ON (Scrobbles.track=Loved_Tracks.track AND Scrobbles.artist=Loved_Tracks.artist AND Scrobbles.userid=Loved_Tracks.userid) WHERE Scrobbles.userid = ' . ($userid) . ' ORDER BY Scrobbles.time DESC LIMIT ' . (int)($number) . ' OFFSET ' . $offset);

				/**


						s.userid,
						s.artist,
						s.track,
						s.album,
						s.time,
						s.mbid,
						a.mbid AS artist_mbid,
						l.mbid AS album_mbid,
						l.image AS album_image,
						l.artwork_license,
						t.license,
						t.mbid AS track_mbid

	                                removed this.

					LEFT JOIN Artist a
						ON s.artist=a.name
					LEFT JOIN Album l
						ON l.artist_name=s.artist
						AND l.name=s.album
					LEFT JOIN Scrobble_Track st
						ON s.stid = st.id
					LEFT JOIN Track t
						ON st.track = t.id

				*/


			} else {
				$res = $adodb->CacheGetAll(60,
					'SELECT *
					FROM Scrobbles ORDER BY time DESC
					LIMIT ' . (int)($number) . ' OFFSET ' . $offset);


				/**

					LEFT JOIN Artist a
						ON s.artist=a.name
					LEFT JOIN Album l
						ON l.artist_name=s.artist
						AND l.name=s.album
					LEFT JOIN Scrobble_Track st
						ON s.stid = st.id
					LEFT JOIN Track t
						ON st.track = t.id

				 */
			}
		} catch (Exception $e) {
			return null;
		}

		if($userid) {
			$username = uniqueid_to_username($userid);
			$userurl = Server::getUserURL($username);
		}

		$result = array();

		foreach ($res as &$i) {
			$row = sanitize($i);

			if(!$userid) {
				$row['username'] = uniqueid_to_username($row['userid']);
				$row['userurl'] = Server::getUserURL($row['username']);
			} else {
				$row['username'] = $username;
				$row['userurl'] = $userurl;
			}
			if ($row['album']) {
				$row['albumurl'] = Server::getAlbumURL($row['artist'], $row['album']);
			}
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);

			$row['timehuman'] = human_timestamp($row['time']);
			$row['timeiso']   = date('c', (int)$row['time']);

			$row['id']        = identifierScrobbleEvent($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_artist'] = identifierArtist($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_track']  = identifierTrack($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_album']  = identifierAlbum($row['username'], $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);

			if (!$row['album_image']) {
				$row['album_image'] = false;
			} else {
				$row['album_image'] = resolve_external_url($row['album_image']);
			}

			if ($row['artwork_license'] == 'amazon') {
				$row['album_image'] = str_replace('SL160', 'SL50', $row['album_image']);
			}

			$row['licenseurl'] = $row['license'];
			$row['license'] = simplify_license($row['licenseurl']);

			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Retrieves a list of popular artists
	 *
	 * @param int $limit The number of artists to return
	 * @param int $offset Skip this number of rows before returning artists
	 * @param bool $streamable Only return streamable artists
	 * @param int $begin Only use scrobbles with time higher than this timestamp
	 * @param int $end Only use scrobbles with time lower than this timestamp
	 * @param int $userid Only return results from this userid
	 * @param int $cache Caching period in seconds
	 * @return array An array of artists ((artist, freq, artisturl) ..) or empty array in case of failure
	 */
	static function getTopArtists($limit = 20, $offset = 0, $streamable = False, $begin = null, $end = null, $userid = null, $cache = 600) {
		global $adodb;

		$query = ' SELECT artist, COUNT(artist) as freq FROM Scrobbles s';

		if ($streamable) {
			$query .= ' INNER JOIN Artist a ON s.artist=a.name WHERE a.streamable=1';
			$andquery = True;
		} else {
			if($begin || $end || $userid) {
				$query .= ' WHERE';
				$andquery = False;
			}
		}

		if($begin) {
			//change time resolution to full hours (for easier caching)
			$begin = $begin - ($begin % 3600);
			
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' time>' . (int)$begin;
		}

		if($end) {
			//change time resolution to full hours (for easier caching)
			$end = $end - ($end % 3600);
			
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' time<' . (int)$end;
		}

		if($userid) {
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' userid=' . (int)$userid;
		}

		$query .= ' GROUP BY artist ORDER BY freq DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$data = $adodb->CacheGetAll($cache, $query);
		} catch (Exception $e) {
			return array();
		}

		$result = array();

		foreach ($data as &$i) {
			$row = sanitize($i);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Retrieves a list of loved artists
	 *
	 * @param int $limit The number of artists to return
	 * @param int $offset Skip this number of rows before returning artists
	 * @param bool $streamable Only return streamable artists
	 * @param int $userid Only return results from this userid
	 * @param int $cache Caching period in seconds
	 * @return array Artists ((artist, freq, artisturl) ..) or empty array in case of failure
	 */
	static function getLovedArtists($limit = 20, $offset = 0, $streamable = False, $userid = null, $cache = 600) {
		global $adodb;

		$query = ' SELECT artist, COUNT(artist) as freq FROM Loved_Tracks lt INNER JOIN Artist a ON a.name=lt.artist';

		if ($streamable) {
			$query .= ' WHERE a.streamable=1';
			$andquery = True;
		} else {
			if ($userid) {
				$query .= ' WHERE';
				$andquery = False;
			}
		}

		if ($userid) {
			$andquery ? $query .= ' AND' : null;
			$query .= ' userid=' . (int)$userid;
		}

		$query .= ' GROUP BY artist ORDER BY freq DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$data = $adodb->CacheGetAll($cache, $query);
		} catch (Exception $e) {
			return array();
		}

		$result = array();

		foreach ($data as &$i) {
			$row = sanitize($i);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Retrieves a list of popular tracks
	 *
	 * @param int $limit The number of tracks to return
	 * @param int $offset Skip this number of rows before returning tracks
	 * @param bool $streamable Only return streamable tracks
	 * @param int $begin Only use scrobbles with time higher than this timestamp
	 * @param int $end Only use scrobbles with time lower than this timestamp
	 * @param int $artist Only return results from this artist
	 * @param int $userid Only return results from this userid
	 * @param int $cache Caching period in seconds
	 * @return array Tracks ((artist, track, freq, listeners, artisturl, trackurl) ..) or empty array in case of failure
	 */
	static function getTopTracks($limit = 20, $offset = 0, $streamable = False, $begin = null, $end = null, $artist = null, $userid = null, $cache = 600) {
		global $adodb;

		$query = 'SELECT s.artist, s.track, count(s.track) AS freq, count(DISTINCT s.userid) AS listeners FROM Scrobbles s';
		
		if ($streamable) {
			$query .= ' WHERE ROW(s.artist, s.track) IN (SELECT artist_name, name FROM Track WHERE streamable=1)';
			$andquery = True;
		} else {
			if($begin || $end || $userid || $artist) {
				$query .= ' WHERE';
				$andquery = False;
			}
		}

		if($begin) {
			//change time resolution to full hours (for easier caching)
			$begin = $begin - ($begin % 3600);
			
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' s.time>' . (int)$begin;
		}

		if($end) {
			//change time resolution to full hours (for easier caching)
			$end = $end - ($end % 3600);
			
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' s.time<' . (int)$end;
		}

		if($userid) {
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' s.userid=' . (int)$userid;
		}

		if($artist) {
			$andquery ? $query .= ' AND' : $andquery = True;
			$query .= ' lower(s.artist)=lower(' . $adodb->qstr($artist) . ')';
		}
	
		$query .= ' GROUP BY s.track, s.artist ORDER BY freq DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$data = $adodb->CacheGetAll($cache, $query);
		} catch (Exception $e) {
			return array();
		}

		$result = array();

		foreach ($data as &$i) {
			$row = sanitize($i);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'], null, $row['track']);
			$result[] = $row;
		}

		return $result;
	}


	/**
	 * Get a list of users with the most listens
	 *
	 * @param int $limit Amount of results to return
	 * @param int $offset Skip this many items before returning results
	 * @param int $streamable Only return results for streamable tracks
	 * @param int $begin Only use scrobbles with time higher than this timestamp
	 * @param int $end Only use scrobbles with time lower than this timestamp
	 * @param string $artist Filter results by this artist
	 * @param string $track Filter result by this track (need $artist to be set)
	 * @param int $cache Caching period in seconds
	 * @return array ((userid, freq, username, userurl) ..)
	 */
	static function getTopListeners($limit = 10, $offset = 0, $streamable = True, $begin = null, $end = null, $artist = null, $track = null, $cache = 600) {
		global $adodb;

		$params = array();
		$query = 'SELECT s.userid, COUNT(*) as freq FROM Scrobbles s';

		if ($streamable) {
			$query .= ' WHERE ROW(s.artist, s.track) IN (SELECT artist_name, name FROM Track WHERE streamable=1)';
			$andquery = True;
		} else {
			if($begin || $end || $artist) {
				$query .= ' WHERE';
				$andquery = False;
			}
		}

		if($begin) {
			//change time resolution to full hours (for easier caching)
			$begin = $begin - ($begin % 3600);
			
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' s.time > ?';
			$params[] = (int)$begin;
		}

		if($end) {
			//change time resolution to full hours (for easier caching)
			$end = $end - ($end % 3600);
			
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' s.time < ?';
			$params[] = (int)$end;
		}

		if($artist) {
			$andquery ? $query .= ' AND' : $andquery = True;
			$query .= ' lower(s.artist)=lower(?)';
			$params[] = $artist;

			if($track) {
				$andquery ? $query .= ' AND' : $andquery = True;
				$query .= ' lower(s.track)=lower(?)';
				$params[] = $track;
			}
		}
	
		$query .= ' GROUP BY s.userid ORDER BY freq DESC LIMIT ? OFFSET ?';
		$params[] = (int)$limit;
		$params[] = (int)$offset;

		try {
			$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
			$res = $adodb->CacheGetAll($cache, $query, $params);
		}catch (Exception $e) {
			return array();
		}

		foreach($res as &$row) {
			$row['username'] = uniqueid_to_username($row['userid']);
			$row['userurl'] = Server::getUserURL($row['username']);
			$result[] = $row;
		}

		return $result;
	}


	/**
	 * Retrieves a list of loved tracks
	 *
	 * @param int $limit The number of tracks to return
	 * @param int $offset Skip this number of rows before returning tracks
	 * @param bool $streamable Only return streamable tracks
	 * @param int $artist Only return results from this artist
	 * @param int $userid Only return results from this userid
	 * @param int $cache Caching period in seconds
	 * @return array Tracks ((artist, track, freq, listeners, artisturl, trackurl) ..) or empty array in case of failure
	 */
	static function getLovedTracks($limit = 20, $offset = 0, $streamable = False, $artist = null, $userid = null, $cache = 600) {
		global $adodb;

		$query = 'SELECT lt.artist, lt.track, max(lt.time) as time, count(lt.track) AS freq FROM Loved_Tracks lt';
		
		if ($streamable) {
			$query .= ' WHERE ROW(lt.artist, lt.track) IN (SELECT artist_name, name FROM Track WHERE streamable=1)';
			$andquery = True;
		} else {
			if($userid || $artist) {
				$query .= ' WHERE';
				$andquery = False;
			}
		}

		if($userid) {
			$andquery ? $query .= ' AND' : $andquery = True ;
			$query .= ' lt.userid=' . (int)$userid;
		}

		if($artist) {
			$andquery ? $query .= ' AND' : $andquery = True;
			$query .= ' lower(lt.artist)=lower(' . $adodb->qstr($artist) . ')';
		}
	
		$query .= ' GROUP BY lt.track, lt.artist ORDER BY freq DESC, time DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$data = $adodb->CacheGetAll($cache, $query);
		} catch (Exception $e) {
			return array();
		}

		$result = array();

		foreach ($data as &$i) {
			$row = sanitize($i);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'], null, $row['track']);
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Get a list of users
	 *
	 * @param string $alpha Search for user names starting with this string
	 */
	static function getUserList($alpha) {
		global $adodb;

		$alpha .= '%';
		$query = 'SELECT username from Users where username LIKE ' . $adodb->qstr($alpha) . ' ORDER BY username ASC';

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll(7200, $query);
		if (!$data) {
			throw new Exception('ERROR ' . $query);
		}

		return $data;
	}

	/**
	 * Retrieves a list of the currently playing tracks
	 *
	 * @param int $number The maximum number of tracks to return
	 * @param string $username The name of the user to retrieve playing tracks for
	 * @return array Now playing data or null in case of failure
	 */
	static function getNowPlaying($number = 1, $username = false) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			if ($username) {
				$data = $adodb->CacheGetAll(1, 'SELECT
							ss.userid,
							n.artist,
							n.track,
							n.album,
							client,
							api_key,
							n.mbid,
							t.license
						FROM Now_Playing n
						LEFT OUTER JOIN Scrobble_Sessions ss
							ON n.sessionid=ss.sessionid
						LEFT OUTER JOIN Track t
							ON lower(n.artist) = lower(t.artist_name)
							AND lower(n.album) = lower(t.album_name)
							AND lower(n.track) = lower(t.name)
							AND lower(n.mbid) = lower(t.mbid)
						WHERE ss.userid= ' . username_to_uniqueid($username) . '
						ORDER BY t.streamable DESC, n.expires DESC LIMIT ' . (int)($number));
			} else {
				$data = $adodb->CacheGetAll(60, 'SELECT
							ss.userid,
							n.artist,
							n.track,
							n.album,
							client,
							n.mbid,
							t.license
						FROM Now_Playing n
						LEFT OUTER JOIN Scrobble_Sessions ss
							ON n.sessionid=ss.sessionid
						LEFT OUTER JOIN Track t
							ON lower(n.artist) = lower(t.artist_name)
							AND lower(n.album) = lower(t.album_name)
							AND lower(n.track) = lower(t.name)
							AND lower(n.mbid) = lower(t.mbid)
						ORDER BY t.streamable DESC, n.expires DESC LIMIT ' . (int)($number));
			}
		} catch (Exception $e) {
			return null;
		}

		$result = array();

		foreach ($data as &$i) {
			$row = sanitize($i);
			
			$client = getClientData($row['client'], $row['api_key']);
			$row['clientcode'] = $client['code'];
			$row['clientapi_key'] = $client['code'];
			$row['clientname'] = $client['name'];
			$row['clienturl'] = $client['url'];
			$row['clientfree'] = $client['free'];
			
			$row['username'] = uniqueid_to_username($row['userid']);
			$row['userurl'] = Server::getUserURL($row['username']);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);
			if ($username) {
				$row['loved'] = $adodb->CacheGetOne(60, 'SELECT Count(*) FROM Loved_Tracks WHERE artist='
					. $adodb->qstr($row['artist'])
					. ' AND track=' . $adodb->qstr($row['track'])
					. ' AND userid=' . $row['userid']);
			}

			// We really want to get an image URI from the database and only fall back to qm50.png if we can't find an image.
			$row['albumart'] = $base_url . 'themes/' . $default_theme . '/images/qm50.png';

			$row['licenseurl'] = $row['license'];
			$row['license'] = simplify_license($row['licenseurl']);

			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Gets the URL to a user's profile page
	 *
	 * The get*URL functions are implemented here rather than in their respective
	 * objects so that we can produce URLs without needing to build whole objects.
	 *
	 * @param string $username The user name we want a URL for
	 * @param string $component Type of URL to return
	 * @param string $params Trailing get parameters
	 * @return string URL to the user's profile
	 */
	static function getUserURL ($username, $component = 'profile', $params = false) {
		global $friendly_urls, $base_url;
		if ($component == 'edit') {
			return $base_url . '/user-edit.php';
		} else if ($component == 'delete') {
			return $base_url . '/delete-profile.php';
		} else if ($friendly_urls) {
			if ($component == 'profile') {
				$component = '';
			} else {
				$component = "/{$component}";
			}
			return $base_url . '/user/' . rewrite_encode($username) . $component . ($params ? '?' . $params : null);
		} else {
			return $base_url . "/user-{$component}.php?user=" . rawurlencode($username) . ($params ? '&' . $params : null);
		}
	}

	/**
	 * Gets the URL to a group's page
	 *
	 * @param string $groupname The group we want a URL for
	 * @return string URL to the group's page
	 */
	static function getGroupURL($groupname) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return $base_url . '/group/' . rewrite_encode($groupname);
		} else {
			return $base_url . '/group.php?group=' . rawurlencode($groupname);
		}
	}

	/**
	 * Gets the URL to an artist's page
	 *
	 * @param string $artist The artist we want a URL for
	 * @param string $component Type of URL to return
	 * @return string URL to the artist's page
	 */
	static function getArtistURL($artist, $component = '') {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return $base_url . '/artist/' . rewrite_encode($artist) . '/' . $component;
		} else {
			if ($component) {
				return $base_url . '/artist-' . $component . '.php?artist=' . rawurlencode($artist);
			} else {
				return $base_url . '/artist.php?artist=' . rawurlencode($artist);
			}
		}
	}
	/**
	 * Gives the URL to the management interface for an artist
	 *
	 * @param string $artist The artist we want a URL for
	 * @return string URL for an artist's management interface
	 */
	static function getArtistManagementURL($artist) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return Server::getArtistURL($artist) . '/manage';
		} else {
			return $base_url . '/artist-manage.php?artist=' . rawurlencode($artist);
		}
	}

	/**
	 * Gives the URL for managers to add a new album to an artist
	 *
	 * @param string $artist The artist we want a URL for
	 * @return string URL for adding albums to an artist
	 */
	static function getAddAlbumURL($artist) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return Server::getArtistURL($artist) . '/album/add';
		} else {
			return $base_url . '/album-add.php?artist=' . rawurlencode($artist);
		}
	}

	/**
	 * Gets the URL to an album's page
	 *
	 * @param string $artist The artist name of the album
	 * @param string $album The name of the album
	 * @return string URL to the album's page
	 */
	static function getAlbumURL($artist, $album) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return $base_url . '/artist/' . rewrite_encode($artist) . '/album/' . rewrite_encode($album);
		} else {
			return $base_url . '/album.php?artist=' . rawurlencode($artist) . '&album=' . rawurlencode($album);
		}
	}

	/**
	 * Gives the URL for managers to add a new track to an album
	 *
	 * @param string $artist The artist name of the album
	 * @param string $album The name of the album
	 * @return string URL for adding tracks to an album
	 */
	static function getAddTrackURL($artist, $album) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return Server::getAlbumURL($artist, $album) . '/track/add';
		} else {
			return $base_url . '/track-add.php?artist=' . rawurlencode($artist) . '&album=' . rawurlencode($album);
		}
	}

	/**
	 * Gets the URL to a track's page
	 *
	 * @param string $artist The artist name of the track
	 * @param string $album The album name of this track (optional)
	 * @param string $track The name of the track
	 * @param string $component Type of page
	 * @return string URL to the track's page
	 */
	static function getTrackURL($artist, $album, $track, $component = '') {
		global $friendly_urls, $base_url;

		if($friendly_urls) {
			$trackurl = $base_url . '/artist/' . rewrite_encode($artist);
			if($album) {
				$trackurl .= '/album/' . rewrite_encode($album);
			}
			$trackurl .= '/track/' . rewrite_encode($track);
			if($component) {
				$trackurl .= '/' . $component;
			}
		} else {
			if($component) {
				$trackurl = $base_url . '/track-' . $component . '.php?artist='	. rawurlencode($artist)
					. '&album=' . rawurlencode($album) . '&track=' . rawurlencode($track);
			} else {
				$trackurl = $base_url . '/track.php?artist=' . rawurlencode($artist)
					. '&album=' . rawurlencode($album) . '&track=' . rawurlencode($track);
			}
		}

		return $trackurl;
	}

	/**
	 * Gets the URL to a track's edit page
	 *
	 * @param string $artist The artist name of the track
	 * @param string $album The album name of this track (optional)
	 * @param string $track The name of the track
	 * @return string URL to the track's edit page
	 */
	static function getTrackEditURL($artist, $album, $track) {
		global $friendly_urls, $base_url;
		if ($friendly_urls && $album) {
			return $base_url . '/artist/' . rewrite_encode($artist) . '/album/' . rewrite_encode($album) . '/track/' . rewrite_encode($track) . '/edit';
		} else if ($friendly_urls) {
			return $base_url . '/artist/' . rewrite_encode($artist) . '/track/' . rewrite_encode($track) . '/edit';
		} else {
			return $base_url . '/track-add.php?artist=' . rawurlencode($artist) . '&album=' . rawurlencode($album) . '&track=' . rawurlencode($track);
		}
	}

	static function getAlbumEditURL($artist, $album) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return $base_url . '/artist/' . rewrite_encode($artist) . '/album/' . rewrite_encode($album) . '/edit';
		} else {
			return $base_url . '/album-add.php?artist=' . rawurlencode($artist) . '&album=' . rawurlencode($album);
		}
	}

	/**
	 * Gets the URL to a tag's page
	 *
	 * @param string $tag The name of the tag
	 * @return string URL to the tag's page
	 */
	static function getTagURL($tag) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return $base_url . '/tag/' . rewrite_encode($tag);
		} else {
			return $base_url . '/tag.php?tag=' . rawurlencode($tag);
		}
	}

	static function getLocationDetails($name) {
		global $adodb;

		if (!$name) {
			return array();
		}

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$rv = $adodb->GetRow('SELECT p.latitude, p.longitude, p.country, c.country_name, c.wikipedia_en '
			. 'FROM Places p '
			. 'LEFT JOIN Countries c ON p.country=c.country '
			. 'WHERE p.location_uri=' . $adodb->qstr($name, 'text'));

		if ($rv) {

			if (!($rv['latitude'] && $rv['longitude'] && $rv['country'])) {

				$parser = ARC2::getRDFXMLParser();
				$parser->parse($name);
				$index = $parser->getSimpleIndex();

				$rv = array(
					'latitude'  => $index[$name]['http://www.w3.org/2003/01/geo/wgs84_pos#lat'][0],
					'longitude' => $index[$name]['http://www.w3.org/2003/01/geo/wgs84_pos#long'][0],
					'country'   => strtoupper(substr($index[$name]['http://www.geonames.org/ontology#inCountry'][0], -2))
					);

				$adodb->Execute(sprintf('UPDATE Places SET latitude=%s, longitude=%s, country=%s WHERE location_uri=%s',
					$adodb->qstr($rv['latitude']),
					$adodb->qstr($rv['longitude']),
					$adodb->qstr($rv['country']),
					$adodb->qstr($name)));
			}
		} else {
			$parser = ARC2::getRDFXMLParser();
			$parser->parse($name);
			$index = $parser->getSimpleIndex();

			$rv = array(
				'latitude'  => $index[$name]['http://www.w3.org/2003/01/geo/wgs84_pos#lat'][0],
				'longitude' => $index[$name]['http://www.w3.org/2003/01/geo/wgs84_pos#long'][0],
				'country'   => strtoupper(substr($index[$name]['http://www.geonames.org/ontology#inCountry'][0], -2))
				);

			$adodb->Execute(sprintf('INSERT INTO Places (location_uri, latitude, longitude, country) VALUES (%s, %s, %s, %s)',
				$adodb->qstr($name),
				$adodb->qstr($rv['latitude']),
				$adodb->qstr($rv['longitude']),
				$adodb->qstr($rv['country'])));
		}

		return $rv;
	}

	/**
	 * Log in to the radio server
	 *
	 * @param string $station The station to be played
	 * @param string $username The user to associate this session with (optional)
	 * @param string $session_id Allows for a custom session id to be set, allowing for compatibility with webservices
	 * @return string Session key to be used for streaming
	 */
	static function getRadioSession($station, $username = false, $session_id = false) {
		global $adodb;
		if (!$session_id) {
			$session_id = md5(mt_rand() . time());
		}
		// Remove any previous station for this session id
		$adodb->Execute('DELETE FROM Radio_Sessions WHERE session = ' . $adodb->qstr($session_id));
		if ($username) {
			$sql = 'INSERT INTO Radio_Sessions(username, session, url, expires) VALUES ('
				. $adodb->qstr($username) . ','
				. $adodb->qstr($session_id) . ','
				. $adodb->qstr($station) . ','
				. (int)(time() + 86400) . ')';
		} else {
			$sql = 'INSERT INTO Radio_Sessions(session, url, expires) VALUES ('
				. $adodb->qstr($session_id) . ','
				. $adodb->qstr($station) . ','
				. (int)(time() + 86400) . ')';
		}
		$res = $adodb->Execute($sql);
		return $session_id;
	}

	/**
	 * Log in to web services
	 *
	 * @param string $username The user to create a session for
	 * @return string The web service session key
	 */
	static function getWebServiceSession($username) {
		global $adodb;
		$sk = md5(mt_rand() . time());
		$token = md5(mt_rand() . time());
		$adodb->Execute('INSERT INTO Auth(token, sk, expires, username) VALUES ('
			. $adodb->qstr($token) . ', '
			. $adodb->qstr($sk) . ', '
			. (int)(time() + 86400) . ', '
			. $adodb->qstr($username) . ')');
		return $sk;
	}

	/**
	 * Get scrobble session ID for a user.
	 *
	 * Gets the most recent scrobble session ID for userid,
	 * or creates a new session ID if it can't find one.
	 *
	 * @param int userid (required)			User ID.
	 * @param string api_key (optional)		Client API key (32 characters)
	 * @param int expire_limit (optional)	Amount of time in seconds before session will expire (defaults to 86400 = 24 hours)
	 * @return string						Scrobble session ID
	 */
	static function getScrobbleSession($userid, $api_key = null, $expire_limit = 86400) {
		global $adodb;
		$query = 'SELECT sessionid FROM Scrobble_Sessions WHERE userid = ? AND expires > ?';
		$params = array( (int) $userid, time());

		if (strlen($api_key) == 32) {
			$query .= ' AND api_key=?';
			$params[] = $api_key;
		} elseif (strlen($api_key) == 3) {
			// api_key is really a 3 char client code (2.0-scrobble-proxy.php sends client code in api_key)
			$query .= ' AND client=?';
			$client_id = $api_key;
			$params[] = $client_id;
			// we dont want to insert a 3 char code as api_key in db
			$api_key = null;
		}

		$sessionid = $adodb->GetOne($query, $params);
		if (!$sessionid) {
			$sessionid = md5(mt_rand() . time());
			$expires = time() + (int) $expire_limit;
			$query = 'INSERT INTO Scrobble_Sessions(userid, sessionid, client, expires, api_key) VALUES (?,?,?,?,?)';
			$params = array($userid, $sessionid, $client_id, $expires, $api_key);
			try {
				$adodb->Execute($query, $params);
			} catch (Exception $e) {
				return null;
			}
		}
		return $sessionid;
	}

	/**
	 * Get all artists
	 *
	 * @return array Artists ordered by name
	 */
	static function getAllArtists() {
		global $adodb;

		$sql = 'SELECT * from Artist ORDER by name';
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$res = $adodb->CacheGetAll(86400, $sql);
		} catch (Exception $e) {
			return null;
		}

		$result = array();
		foreach ($res as &$i) {
			$row = sanitize($i);

			$row['artisturl'] = Server::getArtistURL($row['name']);
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Search for users, artists or tags
	 *
	 * Does a lower-case search of %search_term%
	 *
	 * @param string $search_term
	 * @param string $search_type Type of search, artist|user|tag
	 * @param int $limit How many items to return
	 * @return array Results
	 */
	static function search($search_term, $search_type, $limit = 40) {
		global $adodb;
		switch ($search_type) {
			case 'artist':
				$table = 'Artist';
				$search_fields[] = 'name';
				$data_fields[] = 'name';
				$data_fields[] = 'bio_summary';
				break;
			case 'user':
				$table = 'Users';
				$search_fields[] = 'username';
				$search_fields[] = 'fullname';
				$data_fields[] = 'username';
				$data_fields[] = 'fullname';
				$data_fields[] = 'bio';
				break;
			case 'tag':
				$table = 'Tags';
				$search_fields[] = 'tag';
				$data_fields[] = 'tag';
				break;
			default:
				return array();
		}

		$sql = 'SELECT DISTINCT ';

		for ($i = 0; $i < count($data_fields); $i++) {
			$sql .= $data_fields[$i];
			if ($i < count($data_fields) - 1) {
				$sql .= ', ';
			}
		}

		$sql .= ' FROM ' . $table . ' WHERE ';

		for ($i = 0; $i < count($search_fields); $i++) {
			if ($i > 0) {
				$sql .= ' OR ';
			}
			$sql .= 'LOWER(' . $search_fields[$i] . ') LIKE LOWER(' . $adodb->qstr('%' . $search_term . '%') . ')';
		}

		$sql .= 'LIMIT ' . $limit;

		$res = $adodb->CacheGetAll(600, $sql);

		$result = array();
		foreach ($res as &$i) {
			$row = sanitize($i);
			switch ($search_type) {
				case 'artist':
					$row['url'] = Server::getArtistURL($row['name']);
					break;
				case 'user':
					$row['url'] = Server::getUserURL($row['username']);
					break;
				case 'tag':
					$row['url'] = Server::getTagURL($row['tag']);
					break;
			}
			$result[] = $row;
		}

		return $result;
	}

	/**
	 * Create a random authentication token and return it
	 *
	 * @return string Token.
	 */
	static function getAuthToken() {
		global $adodb;

		$key = md5(time() . rand());
		$expires = (int) (time() + 3600);

		$query = 'INSERT INTO Auth(token, expires) VALUES(?,?)';
		$params = array($key, $expires);
		try {
			$adodb->Execute($query, $params);
			return $key;
		} catch (Exception $e) {
			reportError($e->getMessage(), $e->getTraceAsString());
		}
	}

}
