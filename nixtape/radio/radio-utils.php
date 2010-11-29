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

	if(ereg('l(ast|ibre)fm://globaltags/(.*)', $url, $regs)) {
		$tag = $regs[2];
		return 'Libre.fm ' . ucwords($tag) . ' Tag Radio';
	}
	if(ereg('l(ast|ibre)fm://artist/(.*)/similarartists', $url, $regs)) {
		$artist = $regs[2];
		return 'Libre.fm ' . ucwords($artist) . ' Similar Artist Radio';
	}
	if(ereg('l(ast|ibre)fm://artist/(.*)', $url, $regs)) {
		$artist = $regs[2];
		return 'Libre.fm ' . ucwords($artist) . ' Artist Radio';
	}
	if(ereg('l(ast|ibre)fm://user/(.*)/loved', $url, $regs)) {
		$user = $regs[2];
		return 'Libre.fm ' . ucwords($user) . '\'s Loved Radio';
	}
	if(ereg('l(ast|ibre)fm://community/loved', $url, $regs)) {
		return 'Libre.fm Community\'s Loved Radio';
	}

	return 'FAILED';
}


function make_playlist($session, $old_format=false) {
	global $adodb, $smarty;

	$row = $adodb->GetRow('SELECT username, url FROM Radio_Sessions WHERE session = ' . $adodb->qstr($session));

	if(!$row) {
		die("BADSESSION\n"); // this should return a blank dummy playlist instead
	}

	$user = false;
	if(!empty($row['username'])) {
		$user = new User($row['username']);
	}

	$url = $row['url'];

	$title = radio_title_from_url($url);
	$smarty->assign('title', $title);

	if(ereg('l(ast|ibre)fm://globaltags/(.*)', $url, $regs)) {
		$tag = $regs[2];
		$res = $adodb->Execute('SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Tags ON Track.name=Tags.track AND Track.artist_name=Tags.artist WHERE streamable=1 AND lower(tag) = ' . $adodb->qstr(mb_strtolower($tag, 'UTF-8')));
	} elseif(ereg('l(ast|ibre)fm://artist/(.*)/similarartists', $url, $regs)) {
		$artist = new Artist($regs[2]);
		$similarArtists = $artist->getSimilar(20);
		$artistsClause = 'lower(artist_name) = ' . $adodb->qstr(mb_strtolower($artist->name, 'UTF-8'));
		for($i = 0; $i < 4; $i++) {
			$r = rand(0, count($similarArtists) - 1);
			$artistsClause .= ' OR lower(artist_name) = ' . $adodb->qstr(mb_strtolower($similarArtists[$r]['artist'], 'UTF-8'));
		}
		$res = $adodb->Execute('SELECT name, artist_name, album_name, duration, streamurl FROM Track WHERE streamable=1 AND ' . $artistsClause);
	} elseif(ereg('l(ast|ibre)fm://artist/(.*)', $url, $regs)) {
		$artist = $regs[2];
		$res = $adodb->Execute('SELECT name, artist_name, album_name, duration, streamurl FROM Track WHERE streamable=1 AND lower(artist_name) = ' . $adodb->qstr(mb_strtolower($artist, 'UTF-8')));
	} elseif(ereg('l(ast|ibre)fm://user/(.*)/(loved|library)', $url, $regs)) {
		$requser = new User($regs[2]);
		$res = $adodb->Execute('SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Loved_Tracks ON Track.artist_name=Loved_Tracks.artist AND Track.name=Loved_Tracks.track WHERE Loved_Tracks.userid=' . $requser->uniqueid . ' AND Track.streamable=1');
	} elseif(ereg('l(ast|ibre)fm://community/loved', $url, $regs)) {
		$res = $adodb->Execute('SELECT Track.name, Track.artist_name, Track.album_name, Track.duration, Track.streamurl FROM Track INNER JOIN Loved_Tracks ON Track.artist_name=Loved_Tracks.artist AND Track.name=Loved_Tracks.track WHERE Track.streamable=1');
	} else {
		die("FAILED\n"); // this should return a blank dummy playlist instead
	}

	$avail = $res->RecordCount();

	$tr[0] = rand(0,$avail-1);
	$tr[1] = rand(0,$avail-1);
	$tr[2] = rand(0,$avail-1);
	$tr[3] = rand(0,$avail-1);
	$tr[4] = rand(0,$avail-1);
	$tr = array_unique($tr);
	// we should probably shuffle these here

	$radiotracks = array();
	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

	for($i=0; $i<count($tr); $i++) {

		$res->Move($tr[$i]);
		$row = $res->FetchRow();

		if($user) {
			$banned = $adodb->GetOne('SELECT COUNT(*) FROM Banned_Tracks WHERE '
				. 'artist = ' . $adodb->qstr($row['artist_name'])
				. 'AND track = ' . $adodb->qstr($row['name'])
				. 'AND userid = ' . $user->uniqueid);
			if ($banned) {
				// This track has been banned by the user, so select another one
				$tr[$i] = rand(0, $avail-1);
				$i--;
				continue;
			}
		}

		$album = new Album($row['album_name'], $row['artist_name']);

		if($row['duration'] == 0) {
			$duration = 180000;
		} else {
			$duration = $row['duration'] * 1000;
		}

		$radiotracks[$i]['location'] = resolve_external_url($row['streamurl']);
		$radiotracks[$i]['title'] = $row['name'];
		$radiotracks[$i]['id'] = "0000";
		$radiotracks[$i]['album'] = $album->name;
		$radiotracks[$i]['creator'] = $row['artist_name'];
		$radiotracks[$i]['duration'] = $duration;
		$radiotracks[$i]['image'] = $album->image;
		$radiotracks[$i]['artisturl'] = Server::getArtistURL($row['artist_name']);
		$radiotracks[$i]['albumurl'] = $album->getURL();
		$radiotracks[$i]['trackurl'] = Server::getTrackURL($row['artist_name'], $album->name, $row['track_name']);
		$radiotracks[$i]['downloadurl'] = Server::getTrackURL($row['artist_name'], $album->name, $row['track_name']);

	}

	$smarty->assign('radiotracks', $radiotracks);

	if($old_format) {
		$smarty->display('radio_oldxspf.tpl');
	} else {
		$smarty->display('radio_xspf.tpl');
	}
}

?>
