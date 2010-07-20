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
require_once($install_path . '/data/User.php');

class UserJSON {

	public static function getInfo($username) {

		$user = new User($username);
		if (!$user) {
			$json_data = array('error' => 6, 'message' => 'No user with that name was found');
			return json_encode($json_data);
		}

		$json_data = array('user' => array(	'name' => $user->name,
							'homepage' => $user->homepage,
							'location' => $user->location,
							'bio' => $user->bio,
							'url' => $user->getURL(),
							'playcount' => $user->getTotalTracks(),
							'profile_create' => strftime('%c', $user->created)));

		if (isset($user->modified))
			$json_data['user']['profile_updated'] = strftime('%c', $user->modified);

		return json_encode($json_data);
	}


	public static function getRecentTracks($u, $limit, $page) {
		global $adodb;

		if (!isset($limit)) {
			$limit = 10;
		}

		$offset = ($page - 1) * $limit;
		$err = 0;
		try {  
			$user = new User($u);
			if($page == 1) {
				$npres = $user->getNowPlaying(1);
			}
			$res = $user->getScrobbles($limit, $offset);
		} catch (exception $e) {
			$err = 1;
		}

		if ($err || !$res) {
			$json_data = array('error' => 7, 'message' => 'Invalid resource specified');
			json_encode($json_data);
		}

		$totalPages = $adodb->GetOne('SELECT COUNT(track) FROM scrobbles WHERE userid = ' . $user->uniqueid);
		$totalPages = ceil($totalPages / $limit);

		$json_data = array();
		$json_data['recenttracks'] = array();
		$json_data['recenttracks']['@attr'] = array('user' => $user->name, 'page' => $page, 'perPage' => $limit, 'totalPages' => $totalPages);
		$json_data['recenttracks']['track'] = array();

		if($npres) {
			foreach($npres as &$row) {
				$track = UserJSON::_getTrack($row);
				$track['nowplaying'] = true;
				$track['time'] = time();
				$json_data['recenttracks']['track'][] = $track;
			}
		}

		foreach($res as &$row) {
			$track = UserJSON::_getTrack($row);
			$json_data['recenttracks']['track'][] = $track;
		}

		return json_encode($json_data);
	}


	private static function _getTrack($row) {
		$track = array();
		$track['artist'] = array('#text' => $row['artist'], 'mbid' => $row['artist_mbid']);
		$track['name'] = $row['track'];
		$track['mbid'] = $row['mbid'];
		$track['album'] = array('#text' => $row['album'], 'mbid' => $row['album_mbid']);
		$track['url'] = Server::getTrackURL($row['artist'], $row['album'], $row['track']);
		$track['data'] = array('#text' => gmdate("d M Y H:i",$row['time']) . " GMT", 'uts' => $row['time']);
		$track['streamable'] = 0;
		return $track;
	}

}
