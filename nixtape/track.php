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

require_once('database.php');
require_once('templating.php');
require_once('data/sanitize.php');
require_once('data/Server.php');
require_once('data/TagCloud.php');

$track = new Track(urldecode($_GET['track']), urldecode($_GET['artist']));


$smarty->assign("track", $track);
$smarty->assign("albumurl", Server::getAlbumURL($track->artist_name, $track->album_name));
$smarty->assign("artisturl", Server::getArtistURL($track->artist_name));

// no idea how this would be track-relevant
$aTagCloud = TagCloud::GenerateTagCloud('Scrobbles', 'artist');
if (!PEAR::isError ($aTagCloud)) {
        $smarty->assign('tagcloud', $aTagCloud);
}

$res = $mdb2->query("SELECT * FROM Track WHERE lower(artist) = " . $mdb2->quote(strtolower($track->artist_name),"text") . " AND lower(name) = " . $mdb2->quote(strtolower($track->name),"text"));

$aOtheralbums = array();
$i = 0;

while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
	$trow = sanitize($row);
	if ($trow["album"]) {
	$aArtistAlbums[$i++] = new Album(row["album"], row["artist"]);
	}
}

$smarty->assign("albums", $aOtherAlbums);

$smarty->display("track.tpl");
?>
