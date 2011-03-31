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
require_once($install_path . '/temp-utils.php'); // this is extremely dodgy and shameful

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
	 * @return An array of scrobbles or null in case of failure
	 */
	static function getRecentScrobbles($number=10, $userid=false, $offset=0) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
		if($userid) {
			$res = $adodb->CacheGetAll(60,
				'SELECT * 
				FROM Scrobbles
				WHERE userid = ' . ($userid) . ' ORDER BY time DESC LIMIT ' . (int)($number) . ' OFFSET ' . $offset);

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
		}
		catch (exception $e) {
			return null;
		}

		foreach($res as &$i) {
			$row = sanitize($i);

			$row['username'] = uniqueid_to_username($row['userid']);
			$row['userurl'] = Server::getUserURL(uniqueid_to_username($row['userid']));
			if ($row['album']) {
				$row['albumurl'] = Server::getAlbumURL($row['artist'], $row['album']);
			}
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);

			$row['timehuman'] = human_timestamp($row['time']);
			$row['timeiso']   = date('c', (int)$row['time']);

			$row['id']        = identifierScrobbleEvent(uniqueid_to_username($row['userid']), $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_artist'] = identifierArtist(uniqueid_to_username($row['userid']), $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_track']  = identifierTrack(uniqueid_to_username($row['userid']), $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);
			$row['id_album']  = identifierAlbum(uniqueid_to_username($row['userid']), $row['artist'], $row['track'], $row['album'], $row['time'], $row['mbid'], $row['artist_mbid'], $row['album_mbid']);

			if($userid) {
				$row['loved'] = $adodb->CacheGetOne(60, 'SELECT Count(*) FROM Loved_Tracks WHERE artist='
						. $adodb->qstr($row['artist'])
						. ' AND track=' . $adodb->qstr($row['track'])
						. ' AND userid=' . $userid);
			}
			if (!$row['album_image']) {
				$row['album_image'] = false;
			} else {
				$row['album_image'] = resolve_external_url($row['album_image']);
			}

			if ($row['artwork_license'] == 'amazon') {
				$row['album_image'] = str_replace('SL160','SL50',$row['album_image']);
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
	 * @param int $number The number of artists to return
	 * @return An array of artists or null in case of failure
	*/
	static function getTopArtists($number=20) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
		$data = $adodb->CacheGetAll(720, 'SELECT COUNT(artist) as c, artist FROM Scrobbles GROUP BY artist ORDER BY c DESC LIMIT 20');
		}
		catch (exception $e) {
			return null;
		}

		foreach($data as &$i) {
			$row = sanitize($i);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$result[] = $row;
		}

		return $result;
	}

	static function getUserList($alpha) {
		global $adodb;

		$query = "SELECT username from users where username LIKE '" . $alpha . "%'";

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll(7200,$query);
		if (!$data) {
			throw new Exception('ERROR ' . $query);
		} 

	}

	/**
	 * Retrieves a list of the currently playing tracks
	 *
	 * @param int $number The maximum number of tracks to return
	 * @return An array of now playing data or null in case of failure
	 */
	static function getNowPlaying($number=1, $username=false) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
		if($username) {
			$data = $adodb->CacheGetAll(1, 'SELECT
						ss.userid,
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
					LEFT OUTER JOIN Scrobble_Sessions ss
						ON n.sessionid=ss.sessionid
					LEFT OUTER JOIN ClientCodes
						ON ss.client=ClientCodes.code
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
						ClientCodes.name,
						ClientCodes.url,
						ClientCodes.free,
						n.mbid,
						t.license
					FROM Now_Playing n
					LEFT OUTER JOIN Scrobble_Sessions ss
						ON n.sessionid=ss.sessionid
					LEFT OUTER JOIN ClientCodes
						ON ss.client=ClientCodes.code
					LEFT OUTER JOIN Track t
						ON lower(n.artist) = lower(t.artist_name)
						AND lower(n.album) = lower(t.album_name)
						AND lower(n.track) = lower(t.name)
						AND lower(n.mbid) = lower(t.mbid)
					ORDER BY t.streamable DESC, n.expires DESC LIMIT ' . (int)($number));
		}
		}
		catch (exception $e) {
			return null;
		}

		foreach($data as &$i) {
			$row = sanitize($i);
			// this logic should be cleaned up and the free/nonfree decision be moved into the smarty templates
			if($row['name'] == '') {
				$clientstr = strip_tags(stripslashes($row['client'])) . ' (unknown, <a href="http://ideas.libre.fm/index.php/Client_Codes">please tell us what this is</a>)';
			} elseif($row["free"] == "Y") {
				$clientstr = '<a href="' . strip_tags(stripslashes($row['url'])) . '">' . strip_tags(stripslashes($row['name'])) . '</a>';
			} else {
				$clientstr = '<a href="http://en.wikipedia.org/wiki/Category:Free_media_players">' . strip_tags(stripslashes($row['name'])) . '</a>';
			}
			$row['clientstr'] = $clientstr;
			$row['username'] = uniqueid_to_username($row['userid']);
			$row['userurl'] = Server::getUserURL($row['username']);
			$row['artisturl'] = Server::getArtistURL($row['artist']);
			$row['trackurl'] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);
			if($username) {
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
	 * The get*URL functions are implemented here rather than in their respective
	 * objects so that we can produce URLs without needing to build whole objects.
	 *
	 * @param string $username The username we want a URL for
	 * @return A string containing URL to the user's profile
	 */
	static function getUserURL ($username, $component='profile')
	{
		global $friendly_urls, $base_url;
		if ($component == 'edit')
		{
			return $base_url . '/user-edit.php';
		} elseif ($component == 'delete') {
			return $base_url . '/delete-profile.php';
		}
		elseif($friendly_urls)
		{
			if ($component == 'profile')
				$component = '';
			else
				$component = "/{$component}";
			return $base_url . '/user/' . urlencode($username) . $component;
		}
		else
		{
			return $base_url . "/user-{$component}.php?user=" . urlencode($username);
		}
	}

	static function getGroupURL($groupname) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . '/group/' . urlencode($groupname);
		} else {
			return $base_url . '/group.php?group=' . urlencode($groupname);
		}
	}

	static function getArtistURL($artist, $component='') {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . '/artist/' . urlencode($artist) . '/' . $component;
		} else {
			if($component) {
				return $base_url . '/artist-' . $component . '.php?artist=' . urlencode($artist);
			} else {
				return $base_url . '/artist.php?artist=' . urlencode($artist);
			}
		}
	}

	static function getArtistManagementURL($artist) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return Server::getArtistURL($artist) . '/manage';
		} else {
			return $base_url . '/artist-manage.php?artist=' . urlencode($artist);
		}
	}

	static function getAddAlbumURL($artist) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return Server::getArtistURL($artist) . '/album/add';
		} else {
			return $base_url . '/album-add.php?artist=' . urlencode($artist);
		}
	}

	static function getAlbumURL($artist, $album) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . '/artist/' . urlencode($artist) . '/album/' . urlencode($album);
		} else {
			return $base_url . '/album.php?artist=' . urlencode($artist) . '&album=' . urlencode($album);
		}
	}
	
	static function getAddTrackURL($artist, $album) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return Server::getAlbumURL($artist, $album) . '/track/add';
		} else {
			return $base_url . '/track-add.php?artist=' . urlencode($artist) . '&album=' . urlencode($album);
		}
	}


	static function getTrackURL($artist, $album, $track) {
		global $friendly_urls, $base_url;
		if ($friendly_urls && $album) {
			return $base_url . '/artist/' . urlencode($artist) . '/album/' . urlencode($album) . '/track/' . urlencode($track);
		} elseif ($friendly_urls) {
			return $base_url . '/artist/' . urlencode($artist) . '/track/' . urlencode($track);
		} else {
			return $base_url . '/track.php?artist=' . urlencode($artist) . '&album=' . urlencode($album) . '&track=' . urlencode($track);
		}
	}

	static function getTrackEditURL($artist, $album, $track) {
		global $friendly_urls, $base_url;
		if ($friendly_urls && $album) {
			return $base_url . '/artist/' . urlencode($artist) . '/album/' . urlencode($album) . '/track/' . urlencode($track) . '/edit';
		} elseif ($friendly_urls) {
			return $base_url . '/artist/' . urlencode($artist) . '/track/' . urlencode($track) . '/edit';
		} else {
			return $base_url . '/track-add.php?artist=' . urlencode($artist) . '&album=' . urlencode($album) . '&track=' . urlencode($track);
		}
	}

	static function getTagURL($tag) {
		global $friendly_urls, $base_url;
		if ($friendly_urls) {
			return $base_url . '/tag/' . urlencode($tag);
		} else {
			return $base_url . '/tag.php?tag=' . urlencode($tag);
		}
	}

	static function getLocationDetails($name) {
		global $adodb;

		if (!$name)
			return array();

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$rv = $adodb->GetRow('SELECT p.latitude, p.longitude, p.country, c.country_name, c.wikipedia_en '
			. 'FROM Places p '
			. 'LEFT JOIN Countries c ON p.country=c.country '
			. 'WHERE p.location_uri=' . $adodb->qstr($name, 'text'));

		if($rv) {

			if (! ($rv['latitude'] && $rv['longitude'] && $rv['country'])) {

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
		}
		else {
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
	 * @return A string containing the session key to be used for streaming
	 */
	static function getRadioSession($station, $username = false, $session_id = false) {
		global $adodb;
		if(!$session_id) {
			$session_id = md5(mt_rand() . time());
		}
		// Remove any previous station for this session id
		$adodb->Execute('DELETE FROM Radio_Sessions WHERE session = ' . $adodb->qstr($session_id));
		if($username) {
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
	 * @return A string containing the web service session key
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


	static function getAllArtists() {
		global $adodb;

		$sql = 'SELECT * from Artist ORDER by name';
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$res = $adodb->CacheGetAll(86400,$sql);
		} catch (exception $e) {
			return null;
		}

		$result = array();
		foreach($res as &$i) {
			$row = sanitize($i);

			$row['artisturl'] = Server::getArtistURL($row['name']);
			$result[] = $row;
		}

		return $result;
	}


	static function search($search_term, $search_type, $limit=40) {
		global $adodb;
		$search_term = strtolower($search_term);
		switch($search_type) {
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

		for($i = 0; $i < count($data_fields); $i++) {
			$sql .= $data_fields[$i];
			if($i < count($data_fields)-1) {
				$sql .= ', ';
			}
		}

		$sql .= ' FROM ' . $table . ' WHERE ';

		for($i = 0; $i < count($search_fields); $i++ ) {
			if ($i > 0) {
				$sql .= ' OR ';
			}
			$sql .= 'LOWER(' . $search_fields[$i] . ') LIKE ' . $adodb->qstr('%' . $search_term . '%');
		}

		$sql .= 'LIMIT ' . $limit;

		$res = $adodb->CacheGetAll(600, $sql);

		$result = array();
		foreach($res as &$i) {
			$row = sanitize($i);
			switch($search_type) {
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

}
