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
require_once('radio-utils.php');

// These deaths should probably just return an empty playlist

if(!isset($_GET['sk']) || !isset($_GET['desktop'])) {
	die("BADSESSION\n"); // this should return a blank dummy playlist instead
}

$session = $_GET['sk'];

$res = $adodb->GetOne('SELECT url FROM Radio_Sessions WHERE session = ' . $adodb->qstr($session));

if(!$res) {
	die("BADSESSION\n"); // this should return a blank dummy playlist instead
}

$url = $res;

$title = radio_title_from_url($url);
$smarty->assign('title', $title);

if(ereg('l(ast|ibre)fm://globaltags/(.*)', $url, $regs)) {
	$tag = $regs[2];
	$res = $adodb->Execute('SELECT Track.name, Track.artist_name, Track.album_name FROM Track INNER JOIN Tags ON Track.name=Tags.track AND Track.artist_name=Tags.artist AND Track.album_name=Tags.album WHERE streamurl<>\'\' AND streamable=1 AND lower(tag) = ' . $adodb->qstr(mb_strtolower($tag, 'UTF-8')));
} elseif(ereg('l(ast|ibre)fm://artist/(.*)/similarartists', $url, $regs)) {
	$artist = $regs[2];
	$res = $adodb->Execute('SELECT name, artist_name, album_name FROM Track WHERE streamurl<>\'\' AND streamable=1 AND lower(artist_name) = ' . $adodb->qstr(mb_strtolower($artist, 'UTF-8')));
} elseif(ereg('l(ast|ibre)fm://user/(.*)/loved', $url, $regs)) {
	$user = new User($regs[2]);
	$res = $adodb->Execute('SELECT Track.name, Track.artist_name, Track.album_name FROM Track INNER JOIN Loved_Tracks ON Track.artist_name=Loved_Tracks.artist AND Track.name=Loved_Tracks.track WHERE Loved_Tracks.userid=' . $user->uniqueid . ' AND Track.streamurl<>\'\' AND Track.streamable=1');
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

	$track = new Track($row['name'], $row['artist_name']);
	$album = new Album($row['album_name'], $row['artist_name']);
	$artist = new Artist($row['artist_name']);

	if($track->duration == 0) {
		$duration = 180000;
	} else {
		$duration = $track->duration * 1000;
	}

	$radiotracks[$i]['location'] = $track->streamurl;
	$radiotracks[$i]['title'] = $track->name;
	$radiotracks[$i]['id'] = "0000";
	$radiotracks[$i]['album'] = $album->name;
	$radiotracks[$i]['creator'] = $artist->name;
	$radiotracks[$i]['duration'] = $duration;
	$radiotracks[$i]['image'] = $album->image;
	$radiotracks[$i]['artisturl'] = $artist->getURL();
	$radiotracks[$i]['albumurl'] = $album->getURL();
	$radiotracks[$i]['trackurl'] = $track->getURL();
	$radiotracks[$i]['downloadurl'] = $track->getURL();

	}

$smarty->assign('radiotracks', $radiotracks);

$smarty->display('radio_oldxspf.tpl');

?>
