<?
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

// fakes the radio xspf generation

require_once('../config.php');
require_once('../database.php');

if(!isset($_GET['sk']) || !isset($_GET['desktop'])) {
	die("Uh-oh\n");
}

echo "<playlist version=\"1\" xmlns:lastfm=\"http://www.audioscrobbler.net/dtd/xspf-lastfm\">\n";
echo "<title></title>\n";
echo "<creator>Last.fm</creator>\n";
echo "<link rel=\"http://www.last.fm/skipsLeft\">9999</link>\n";
echo "<trackList>\n";

$res = $mdb2->query("SELECT name, artist, album, duration, downloadurl, streamurl FROM Track WHERE license='http://creativecommons.org/licenses/by/3.0/' AND streamurl LIKE 'jamendo://track/stream/%' LIMIT 5");

while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {

$name = $row["name"];
$artist = $row["artist"];
$album = $row["album"];
$duration = $row["duration"];
$downloadurl = $row["downloadurl"];
$streamurl = $row["streamurl"];

if(ereg("jamendo://track/stream/(.*)", $streamurl, $regs)) {
$jmtrack = $regs[1];

echo "    <track>\n";
echo "        <location>http://api.jamendo.com/get2/stream/track/redirect/?id=$jmtrack&amp;streamencoding=ogg2</location>\n";
echo "        <title>$name</title>\n";
echo "        <id>$jmtrack</id>\n";
echo "        <album>$album</album>\n";
echo "        <creator>$artist</creator>\n";
echo "        <duration>$duration</duration>\n";
echo "        <image></image>\n";
echo "        <lastfm:trackauth>00000</lastfm:trackauth>\n";
echo "        <lastfm:albumId>0000000</lastfm:albumId>\n";
echo "        <lastfm:artistId>00000000</lastfm:artistId>\n";
echo "                <link rel=\"http://www.last.fm/artistpage\"></link>\n";
echo "        <link rel=\"http://www.last.fm/albumpage\"></link>\n";
echo "        <link rel=\"http://www.last.fm/trackpage\"></link>\n";
echo "        <link rel=\"http://www.last.fm/buyTrackURL\"></link>\n";
echo "        <link rel=\"http://www.last.fm/buyAlbumURL\"></link>\n";
echo "        <link rel=\"http://www.last.fm/freeTrackURL\">$downloadurl</link>\n";
echo "    </track>\n";

}

}

echo "</trackList>\n";
echo "</playlist>\n";
?>
