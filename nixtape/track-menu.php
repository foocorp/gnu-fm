<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2012 Free Software Foundation, Inc

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

function track_menu($track, $active_page) {
	global $this_user;

	$submenu = array(
		array('name' => _('Overview'), 'url' => $track->getURL()),
		array('name' => _('Tag'), 'url' => $track->getURL('tag')),
		array('name' => _('Love'), 'url' => $track->getURL('love')),
	);
	
	foreach($submenu as &$item) {
		$item['active'] = ($item['name'] == $active_page);
	}
	return $submenu;
}

// Create Artist, Album and Track objects
try {
	$track = new Track($_GET['track'], $_GET['artist']);
	$smarty->assign('track', $track);
} catch (Exception $e) {
	$smarty->assign('pageheading', 'Track not found.');
	$smarty->assign('details', 'The track ' . $_GET['track'] . ' by artist ' . $_GET['artist'] . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

try {
	$album = new Album($track->album_name, $track->artist_name);
	$smarty->assign('album', $album);
} catch (Exception $e) {}

try {
	$artist = new Artist($track->artist_name);
	$smarty->assign('artist', $artist);
} catch (Exception $e) {
	$smarty->assign('pageheading', 'Artist not found.');
	$smarty->assign('details', 'The artist ' . $track->artist_name . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

if (isset($this_user) && $this_user->manages($artist->name)) {
	$smarty->assign('edit_link', $track->getEditURL());
}

$smarty->assign('pagetitle', $track->artist_name . ' : ' . $track->name);
$smarty->assign('headerfile', 'track-header.tpl');
