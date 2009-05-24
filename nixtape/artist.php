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

try {
	$artist = new Artist($_GET['artist']);
} catch (exception $e) {
        $smarty->assign('error', 'Artist not found.');
        $smarty->assign('details', 'The artist '.($_GET['artist']).' was not found in the database.');
	$smarty->display("error.tpl");
	die();
}

$smarty->assign('name', $artist->name);
$smarty->assign('id', $artist->id);
$smarty->assign('bio_summary', $artist->bio_summary);

$aTagCloud = TagCloud::GenerateTagCloud(TagCloud::scrobblesTable(), 'artist');
if ($aTagCloud) {
        $smarty->assign('tagcloud', $aTagCloud);
}

$aArtistAlbums = $artist->getAlbums();
if ($aArtistAlbums) {
	$smarty->assign('albums', $aArtistAlbums);
}

$smarty->assign('extra_head_links', array(
		array(
			'rel' => 'meta',
			'type' => 'application/rdf+xml' ,
			'title' => 'FOAF',
			'href' => $base_url.'/rdf.php?fmt=xml&page='.urlencode(str_replace($base_url, '', $artist->getURL()))
			)
	));

$smarty->display("artist.tpl");

?>
