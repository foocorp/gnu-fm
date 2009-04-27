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

require_once('../database.php');
require_once('../templating.php');
require_once('../data/Track.php');
require_once("radio-utils.php");

// These deaths should probably just return an empty playlist

if(!isset($_GET['sk']) || !isset($_GET['desktop'])) {
	die("BADSESSION\n"); // this should return a blank dummy playlist instead
}

$session = $_GET["sk"];

$res = $mdb2->query("SELECT url FROM Radio_Sessions WHERE session = " . $mdb2->quote($session, "text"));

if(!$res->numRows()) {
	die("BADSESSION\n"); // this should return a blank dummy playlist instead
}

$url = $res->fetchOne(0);

$title = radio_title_from_url($url);
$smarty->assign('title', $title);

if(ereg("l(ast|ibre)fm://globaltags/(.*)", $url, $regs)) {
	$tag = $regs[2];
	$res = $mdb2->query("SELECT Track.name, Track.artist, Track.album FROM Track INNER JOIN Tags ON Track.name=Tags.track AND Track.artist=Tags.artist AND Track.album=Tags.album WHERE streamurl<>'' AND streamable=1 AND lower(tag) = " . $mdb2->quote(strtolower($tag), "text"));
} elseif("l(ast|ibre)fm://artist/(.*)/similarartists", $url, $regs)) {
	$artist = $regs[2];
	$res = $mdb2->query("SELECT name, artist, album FROM Track WHERE streamurl<>'' AND streamable=1 AND lower(artist) = " . $mdb2->quote(strtolower($artist), "text"));
} else {
	die("FAILED\n"); // this should return a blank dummy playlist instead
}


$avail = $res->numRows();

// This needs some kind of deduplication among other things
$tr[0] = rand(1,$avail);
$tr[1] = rand(1,$avail);
$tr[2] = rand(1,$avail);
$tr[3] = rand(1,$avail);
$tr[4] = rand(1,$avail);
sort($tr);

$radiotracks = array();

	for($i=0; $i<5; $i++) {

	$res->seek($tr[$i]);
	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

	$track = new Track($row["name"], $row["artist"]);
	$album = new Album($row["album"], $row["artist"]);
	$artist = new Artist($row["artist"]);

	if($track->duration == 0) {
		$duration = 180000;
	} else {
		$duration = $track->duration * 1000;
	}

	$radiotracks[$i]["location"] = $track->streamurl;
	$radiotracks[$i]["title"] = $track->name;
	$radiotracks[$i]["id"] = "0000";
	$radiotracks[$i]["album"] = $album->name;
	$radiotracks[$i]["creator"] = $artist->name;
	$radiotracks[$i]["duration"] = $duration;
	$radiotracks[$i]["image"] = $album->image;
	$radiotracks[$i]["artisturl"] = $artist->getURL();
	$radiotracks[$i]["albumurl"] = $album->getURL();
	$radiotracks[$i]["trackurl"] = $track->getURL();
	$radiotracks[$i]["downloadurl"] = $track->getURL();

	}

$smarty->assign('radiotracks', $radiotracks);

$smarty->display('radio_xspf.tpl');

?>
