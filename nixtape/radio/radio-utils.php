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

require_once('../database.php');
require_once('../templating.php');
require_once('../data/Track.php');
require_once('../data/Server.php');
require_once('../utils/resolve-external.php');


function radio_title_from_url($url) {

	if (preg_match('@l(ast|ibre)fm://globaltags/(.*)/loved@', $url, $regs)) {
		$tag = $regs[2];
		return 'Libre.fm ' . ucwords($tag) . ' Loved Tag Radio';
	}
	if (preg_match('@l(ast|ibre)fm://globaltags/(.*)@', $url, $regs)) {
		$tag = $regs[2];
		return 'Libre.fm ' . ucwords($tag) . ' Tag Radio';
	}
	if (preg_match('@l(ast|ibre)fm://artist/(.*)/similarartists@', $url, $regs)) {
		$artist = $regs[2];
		return 'Libre.fm ' . ucwords($artist) . ' Similar Artist Radio';
	}
	if (preg_match('@l(ast|ibre)fm://artist/(.*)@', $url, $regs)) {
		$artist = $regs[2];
		return 'Libre.fm ' . ucwords($artist) . ' Artist Radio';
	}
	if (preg_match('@l(ast|ibre)fm://user/(.*)/loved@', $url, $regs)) {
		$user = $regs[2];
		return 'Libre.fm ' . ucwords($user) . '\'s Loved Radio';
	}
	if (preg_match('@l(ast|ibre)fm://user/(.*)/recommended@', $url, $regs)) {
		$user = $regs[2];
		return 'Libre.fm ' . ucwords($user) . '\'s Recommended Radio';
	}
	if (preg_match('@l(ast|ibre)fm://user/(.*)/mix@', $url, $regs)) {
		$user = $regs[2];
		return 'Libre.fm ' . ucwords($user) . '\'s Mix Radio';
	}
	if (preg_match('@l(ast|ibre)fm://user/(.*)/neighbours@', $url, $regs)) {
		$user = $regs[2];
		return 'Libre.fm ' . ucwords($user) . '\'s Neighbourhood radio';
	}
	if (preg_match('@l(ast|ibre)fm://community/loved@', $url, $regs)) {
		return 'Libre.fm Community\'s Loved Radio';
	}

	return 'FAILED';
}


function make_playlist($session, $old_format = false) {
	global $adodb, $smarty;

	$row = $adodb->GetRow('SELECT username, url FROM Radio_Sessions WHERE session = ' . $adodb->qstr($session));

	if (!$row) {
		die("BADSESSION\n"); // this should return a blank dummy playlist instead
	}

	$user = false;
	if (!empty($row['username'])) {
		try {
			$user = new User($row['username']);
		} catch (Exception $e) {
			// No such user.
			// This shouldn't happen; but if it does, banned tracks won't be filtered.
		}
	}

	$url = $row['url'];

	$title = radio_title_from_url($url);
	$smarty->assign('title', $title);
	
	if (preg_match('@l(ast|ibre)fm://globaltags/(.*)/loved@', $url, $regs)) {
		$tag = $regs[2];
		$res = $adodb->CacheGetAll(7200, 'SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Tags ON Track.name=Tags.track AND Track.artist_name=Tags.artist INNER JOIN Loved_Tracks ON Track.artist_name=Loved_Tracks.artist AND Track.name=Loved_Tracks.track WHERE streamable=1 AND lower(tag) = lower(' . $adodb->qstr($tag) . ')');
	} else if (preg_match('@l(ast|ibre)fm://globaltags/(.*)@', $url, $regs)) {
		$tag = $regs[2];
		$res = $adodb->CacheGetAll(7200, 'SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Tags ON Track.name=Tags.track AND Track.artist_name=Tags.artist WHERE streamable=1 AND lower(tag) = lower(' . $adodb->qstr($tag) . ')');
	} else if (preg_match('@l(ast|ibre)fm://artist/(.*)/similarartists@', $url, $regs)) {
		try {
			$artist = new Artist($regs[2]);
		} catch (Exception $e) {
			die("FAILED\n"); // this should return a blank dummy playlist instead
		}
		$similarArtists = $artist->getSimilar(20);
		$res = get_artist_selection($similarArtists, $artist);
	} else if (preg_match('@l(ast|ibre)fm://artist/(.*)@', $url, $regs)) {
		$artist = $regs[2];
		$res = $adodb->CacheGetAll(7200, 'SELECT name, artist_name, album_name, duration, streamurl FROM Track WHERE streamable=1 AND lower(artist_name) = lower(' . $adodb->qstr($artist) . ')');
	} else if (preg_match('@l(ast|ibre)fm://user/(.*)/(loved|library|personal)@', $url, $regs)) {
		try {
			$requser = new User($regs[2]);
		} catch (Exception $e) {
			die("FAILED\n"); // this should return a blank dummy playlist instead
		}
		$res = get_loved_tracks(array($requser->uniqueid));
	} else if (preg_match('@l(ast|ibre)fm://user/(.*)/recommended@', $url, $regs)) {
		try {
			$requser = new User($regs[2]);
		} catch (Exception $e) {
			die("FAILED\n"); // this should return a blank dummy playlist instead
		}
		$recommendedArtists = $requser->getRecommended(8, true);
		$res = get_artist_selection($recommendedArtists);
	} else if (preg_match('@l(ast|ibre)fm://user/(.*)/mix@', $url, $regs)) {
		try {
			$requser = new User($regs[2]);
		} catch (Exception $e) {
			die("FAILED\n"); // this should return a blank dummy playlist instead
		}
		$recommendedArtists = $requser->getRecommended(8, true);
		$res = get_loved_tracks(array($requser->uniqueid)) + get_artist_selection($recommendedArtists);
	} else if (preg_match('@l(ast|ibre)fm://user/(.*)/neighbours@', $url, $regs)) {
		try {
			$requser = new User($regs[2]);
		} catch (Exception $e) {
			die("FAILED\n"); // this should return a blank dummy playlist instead
		}

		$neighbours = $requser->getNeighbours();
		$userids = array();
		foreach ($neighbours as $neighbour) {
			$userids[] = $neighbour['userid'];
		}
		$res = get_loved_tracks($userids);
	} else if (preg_match('@l(ast|ibre)fm://community/loved@', $url, $regs)) {
		$res = $adodb->CacheGetAll(7200, 'SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Loved_Tracks ON Track.artist_name=Loved_Tracks.artist AND Track.name=Loved_Tracks.track WHERE Track.streamable=1');
	} else {
		die("FAILED\n"); // this should return a blank dummy playlist instead
	}

	$num_tracks = count($res) > 5 ? 5 : count($res);

	$used_tracks = array();
	$radiotracks = array();

	for ($i = 0; $i < $num_tracks; $i++) {

		$tracks_left = true;
		do {
			$random_track = rand(0, count($res) - 1);
			$banned = false;
			$row = $res[$random_track];
			if (count($res) == count($used_tracks)) {
				// Ran out of unique, unbanned tracks
				$tracks_left = false;
			}
			if ($user) {
				// See if a track has been banned by the user, if so select another one
				$banned = $adodb->GetOne('SELECT COUNT(*) FROM Banned_Tracks WHERE '
					. 'artist = ' . $adodb->qstr($row['artist_name'])
					. 'AND track = ' . $adodb->qstr($row['name'])
					. 'AND userid = ' . $user->uniqueid);
				if ($banned && !in_array($random_track, $used_tracks)) {
					$used_tracks[] = $random_track;
				}
			}
		} while ((in_array($random_track, $used_tracks) || $banned) && $tracks_left);
		if (!$tracks_left) {
			break;
		}

		$used_tracks[] = $random_track;

		$album = false;
		if (isset($row['album_name'])) {
			$album = new Album($row['album_name'], $row['artist_name']);
		}

		if ($row['duration'] == 0) {
			$duration = 180000;
		} else {
			$duration = $row['duration'] * 1000;
		}

		$radiotracks[$i]['location'] = resolve_external_url($row['streamurl']);
		$radiotracks[$i]['title'] = $row['name'];
		$radiotracks[$i]['id'] = '0000';
		if ($album) {
			$radiotracks[$i]['album'] = $album->name;
		} else {
			$radiotracks[$i]['album'] = '';
		}
		$radiotracks[$i]['creator'] = $row['artist_name'];
		$radiotracks[$i]['duration'] = $duration;
		if ($album) {
			$radiotracks[$i]['image'] = $album->image;
		} else {
			$radiotracks[$i]['image'] = '';
		}
		$radiotracks[$i]['artisturl'] = Server::getArtistURL($row['artist_name']);
		if ($album) {
			$radiotracks[$i]['albumurl'] = $album->getURL();
			$radiotracks[$i]['trackurl'] = Server::getTrackURL($row['artist_name'], $album->name, $row['name']);
			$radiotracks[$i]['downloadurl'] = Server::getTrackURL($row['artist_name'], $album->name, $row['name']);
		} else {
			$radiotracks[$i]['albumurl'] = '';
			$radiotracks[$i]['trackurl'] = Server::getTrackURL($row['artist_name'], false, $row['name']);
			$radiotracks[$i]['downloadurl'] = Server::getTrackURL($row['artist_name'], false, $row['name']);
		}
	}

	$smarty->assign('radiotracks', $radiotracks);

	if ($old_format) {
		$smarty->display('radio_oldxspf.tpl');
	} else {
		$smarty->display('radio_xspf.tpl');
	}
}


function get_artist_selection($artists, $artist = false) {
	global $adodb;

	$artistsClause = '( ';
	if ($artist) {
		$artistsClause = 'lower(artist_name) = lower(' . $adodb->qstr($artist->name) . ')';
	}
	for ($i = 0; $i < 8; $i++) {
		$r = rand(0, count($artists) - 1);
		if ($i != 0 || $artist) {
			$artistsClause .= ' OR ';
		}
		$artistsClause .= 'lower(artist_name) = lower(' . $adodb->qstr($artists[$r]['artist']) . ')';
	}
	$artistsClause .= ' )';

	return $adodb->CacheGetAll(7200, 'SELECT name, artist_name, album_name, duration, streamurl FROM Track WHERE streamable=1 AND ' . $artistsClause);
}

/**
 * Get the loved tracks for a list of users
 *
 * @param array An array of userids (integers).
 * @return array An array of track details.
 */
function get_loved_tracks($users) {
	global $adodb;
	$userclause = '( ';
	for ($i = 0; $i < count($users); $i++) {
		$userclause .= 'Loved_Tracks.userid = ' . $users[$i];
		if ($i < count($users) - 1) {
			$userclause .= ' OR ';
		}
	}
	$userclause .= ' )';

	return $adodb->CacheGetAll(7200, 'SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Loved_Tracks ON Track.artist_name=Loved_Tracks.artist AND Track.name=Loved_Tracks.track WHERE ' . $userclause . ' AND Track.streamable=1');
}

