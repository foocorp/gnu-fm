<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2013 Free Software Foundation, Inc

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
	);
	
	foreach($submenu as &$item) {
		$item['active'] = ($item['name'] == $active_page);
	}
	return $submenu;
}

try {
	$album = new Album($_GET['album'], $_GET['artist']);
} catch (Exception $e) {
	$smarty->assign('pageheading', 'Album not found.');
	$smarty->assign('details', 'The album ' . $_GET['album'] . ' by artist ' . $_GET['artist'] . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

try {
	$artist = new Artist($album->artist_name);
} catch (Exception $e) {
	$smarty->assign('pageheading', 'Artist not found.');
	$smarty->assign('details', 'The artist ' . $track->artist_name . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

if (isset($this_user) && $this_user->manages($artist->name)) {
	$smarty->assign('edit_link', $album->getEditURL());
	$smarty->assign('add_track_link', $album->getAddTrackURL());
}

$smarty->assign('pagetitle', $artist->name . ' : ' . $album->name);
$smarty->assign('headerfile', 'album-header.tpl');
