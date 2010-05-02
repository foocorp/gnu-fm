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

require_once('database.php');
require_once('templating.php');
require_once('data/sanitize.php');
require_once('data/Server.php');
require_once('data/TagCloud.php');

$track = new Track(urldecode($_GET['track']), urldecode($_GET['artist']));
$smarty->assign('track', $track);

$album = new Album($track->album_name, $track->artist_name);
$smarty->assign('album', $album);

try {
	$artist = new Artist($track->artist_name);
} catch (exception $e) {
	$smarty->assign('error', 'Artist not found.');
	$smarty->assign('details', 'The artist ' . $track->artist_name . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

$smarty->assign('artist', $artist);

// no idea how this would be track-relevant
try {
$aTagCloud = TagCloud::GenerateTagCloud(TagCloud::scrobblesTable(), 'artist');
        $smarty->assign('tagcloud', $aTagCloud);
} catch (exception $e) {}

$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
$res = $adodb->GetAll('SELECT * FROM Track WHERE lower(artist_name) = ' . $adodb->qstr(mb_strtolower($track->artist_name, 'UTF-8')) . ' AND lower(name) = ' . $adodb->qstr(mb_strtolower($track->name, 'UTF-8')));

$aOtherAlbums = array();

foreach($res as &$trow) {
	if ($trow['album']) {
		$aOtherAlbums[] = new Album($trow['album'], $trow['artist']);
	}
}

if($track->duration) {
	// Give the duration in MM:SS
	$mins = floor($track->duration / 60);
	$sec = floor($track->duration % 60);
	if (strlen($sec) == 1) { $sec = "0" . $sec; }
	$duration = $mins . ":" . $sec;
	$smarty->assign('duration', $duration);
}

$smarty->assign('albums', $aOtherAlbums);

$smarty->assign('extra_head_links', array(
		array(
			'rel' => 'meta',
			'type' => 'application/rdf+xml' ,
			'title' => 'Track Metadata',
			'href' => $base_url.'/rdf.php?fmt=xml&page='.urlencode(str_replace($base_url, '', $track->getURL()))
			)
	));

$smarty->display('track.tpl');
?>
