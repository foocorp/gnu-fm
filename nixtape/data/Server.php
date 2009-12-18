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
require_once($install_path . '/../turtle/temp-utils.php'); // this is extremely dodgy and shameful

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
	static function getRecentScrobbles($number=10, $userid=false) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
		if($userid) {
			$res = $adodb->CacheGetAll(60,
				'SELECT
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
				FROM Scrobbles s
				WHERE s.userid = ' . ($userid) . '
				ORDER BY
					s.time DESC
				LIMIT ' . (int)($number));

			/**

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
				'SELECT
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
				FROM Scrobbles s
				ORDER BY
					s.time DESC
				LIMIT ' . (int)($number));


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

	/**
	 * Retrieves a list of the currently playing tracks
	 *
	 * @param int $number The maximum number of tracks to return
	 * @return An array of now playing data or null in case of failure
	 */
	static function getNowPlaying($number, $username=false) {
		global $adodb;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
		if($username) {
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
			return $base_url . '/user/' . rawurlencode($username) . $component;
		}
		else
		{
			return $base_url . "/user-{$component}.php?user=" . rawurlencode($username);
		}
	}

	static function getGroupURL($groupname) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . '/group/' . rawurlencode($groupname);
		} else {
			return $base_url . '/group.php?group=' . rawurlencode($groupname);
		}
	}

	static function getArtistURL($artist) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . '/artist/' . rawurlencode($artist);
		} else {
			return $base_url . '/artist.php?artist=' . rawurlencode($artist);
		}
	}

	static function getAlbumURL($artist, $album) {
		global $friendly_urls, $base_url;
		if($friendly_urls) {
			return $base_url . '/artist/' . rawurlencode($artist) . '/album/' . rawurlencode($album);
		} else {
			return $base_url . '/album.php?artist=' . rawurlencode($artist) . '&album=' . rawurlencode($album);
		}
	}

	static function getTrackURL($artist, $album, $track) {
		global $friendly_urls, $base_url;
		if ($friendly_urls && $album) {
			return $base_url . '/artist/' . rawurlencode($artist) . '/album/' . rawurlencode($album) . '/track/' . rawurlencode($track);
		} elseif ($friendly_urls) {
			return $base_url . '/artist/' . rawurlencode($artist) . '/track/' . rawurlencode($track);
		} else {
			return $base_url . '/track.php?artist=' . rawurlencode($artist) . '&album=' . rawurlencode($album) . '&track=' . rawurlencode($track);
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
	 * @return A string containing the session key to be used for streaming
	 */
	static function getRadioSession($station, $username = false) {
		global $adodb;
		$session_id = md5(mt_rand() . time());
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


	static function getAllArtists() {
		global $adodb;

		$sql = 'SELECT * from Artist ORDER by name';
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$res = $adodb->CacheGetAll(86400,$sql);
		} catch (exception $e) {
			return null;
		}

		foreach($res as &$i) {
			$row = sanitize($i);

			$row['artisturl'] = Server::getArtistURL($row['name']);
		}

		return $res;
	}

}
