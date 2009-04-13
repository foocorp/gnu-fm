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
require_once('../data/Track.php');
require_once("radio-utils.php");

if(!isset($_GET['sk']) || !isset($_GET['desktop'])) {
	die("BADSESSION\n");
}

$session = $_GET["sk"];

$res = $mdb2->query("SELECT url FROM Radio_Sessions WHERE session = " . $mdb2->quote($session, "text"));

if(!$res->numRows()) {
	die("BADSESSION\n");
}

$url = $res->fetchOne(0);

$title = radio_title_from_url($url);

echo "<playlist version=\"1\" xmlns:lastfm=\"http://www.audioscrobbler.net/dtd/xspf-lastfm\">\n";
echo "<title>$title</title>\n";
echo "<creator>libre.fm</creator>\n";
echo "<link rel=\"http://www.last.fm/skipsLeft\">9999</link>\n";
echo "<trackList>\n";

if(ereg("l(ast|ibre)fm://globaltags/(.*)", $url, $regs)) {
	$tag = $regs[2];
} else {
	die("FAILED\n");
}

// This needs some kind of randomization
$res = $mdb2->query("SELECT Track.name, Track.artist, Track.album FROM Track LEFT OUTER JOIN Tags ON Track.name=Tags.track AND Track.artist=Tags.artist AND Track.album=Tags.album WHERE streamurl<>'' AND tag = " . $mdb2->quote($tag, "text") . " LIMIT 5");

while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {

	$track = new Track($row["name"], $row["artist"]);
	$album = new Album($row["album"], $row["artist"]);
	$artist = new Artist($row["artist"]);

	if($track->duration == 0) {
		$track->duration = 1800;
	}

	echo "    <track>\n";
	echo "        <location>" . htmlentities($track->streamurl) . "</location>\n";
	echo "        <title>" . $track->name . "</title>\n";
	echo "        <id>" . $track->name . "</id>\n";
	echo "        <album>" . $album->name . "</album>\n";
	echo "        <creator>" . $artist->name . "</creator>\n";
	echo "        <duration>" . $track->duration . "</duration>\n";
	echo "        <image>".  $album->image . "</image>\n";
	echo "        <link rel=\"http://www.last.fm/artistpage\">" . htmlentities($artist->getURL()) . "</link>\n";
	echo "        <link rel=\"http://www.last.fm/albumpage\">" . htmlentities($album->getURL()) . "</link>\n";
	echo "        <link rel=\"http://www.last.fm/trackpage\">" . htmlentities($track->getURL()) . "</link>\n";
	echo "        <link rel=\"http://www.last.fm/buyTrackURL\"></link>\n";
	echo "        <link rel=\"http://www.last.fm/buyAlbumURL\"></link>\n";
	echo "        <link rel=\"http://www.last.fm/freeTrackURL\">" . htmlentities($track->downloadurl) . "</link>\n";
	echo "    </track>\n";

	}

echo "</trackList>\n";
echo "</playlist>\n";
?>
