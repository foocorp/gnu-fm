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

require_once('data/Library_page.php');
require_once('data/Artist.php');
require_once('data/Album.php');
require_once('data/Track.php');

if (!isset($_GET['user']) && $logged_in == false) {
	$smarty->assign('pageheading', 'Error!');
	$smarty->assign('details', 'User not set! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

try {
	$user = new User($_GET['user']);
} catch (Exception $e) {
	$smarty->assign('pageheading', 'User not found');
	$smarty->assign('details', 'Shall I call in a missing persons report?');
	$smarty->display('error.tpl');
	die();
}

$submenu = user_menu($user, 'Library');
$smarty->assign('submenu', $submenu);

$page = new Library_page($user);
$smarty->assign('me', $user);
if ($page->section == 'music') {
	if (isset($_GET['artist']) && (!isset($_GET['album']) && !isset($_GET['track']))) {
		// Music -> Artist name
		$page->getAlbums();
		$page->getTracks();
		$smarty->assign('page', $page);
		$smarty->display('user-library-artist.tpl');

	} else if ((isset($_GET['artist']) && isset($_GET['album'])) && !isset($_GET['track'])) {
		// Music -> Artist name -> Album name
		$page->getTracks();
		$smarty->assign('page', $page);
		$smarty->display('user-library-album.tpl');

	} else if ((isset($_GET['artist']) && isset($_GET['track'])) && !isset($_GET['album'])) {
		// Music -> Artist name -> Track name
		$smarty->assign('page', $page);
		$smarty->display('user-library-track.tpl');

	} else {
		// Music -> Artists
		try {
			$page->getArtists();
		} catch (Exception $e) {
			$smarty->assign('details', $e->getMessage());
			$smarty->display('error.tpl');
			die();
		}
		$smarty->assign('page', $page);
		$smarty->display('user-library-music.tpl');
	}

} else if ($page->section == 'scrobbles') {
	if (isset($_POST['removescrobble'])) {
		Library::removeScrobble($this_user->uniqueid, $_POST['timestamp'], $_POST['artist'], $_POST['track']);
		$nocache = true;
	}
	$page->getScrobbles($nocache);
	$smarty->assign('page', $page);
	$smarty->display('user-library-scrobbles.tpl');

} else if ($page->section == 'loved') {
	if (isset($_POST['unlove'])) {
		$track = new Track($_POST['track'], $_POST['artist']);
		$track->unlove($this_user->uniqueid);
		$nocache=true;
	}
	$page->getLovedTracks($nocache);
	$smarty->assign('page', $page);
	$smarty->display('user-library-loved.tpl');

} else if ($page->section == 'banned') {
	if (isset($_POST['unban'])) {
		$track = new Track($_POST['track'], $_POST['artist']);
		$track->unban($this_user->uniqueid);
		$nocache = true;
	}
	$page->getBannedTracks($nocache);
	$smarty->assign('page', $page);
	$smarty->display('user-library-banned.tpl');

} else if ($page->section == 'tags') {
	if (isset($_POST['trackremovetag'])) {
		// remove track tag
		$track = new Track($_POST['removetrack'], $_POST['removeartist']);
		$track->removeTag($_POST['removetag'], $this_user->uniqueid);
		$nocache=true;
	} else if (isset($_POST['albumremovetag'])) {
		// remove album tag
		$album = new Album($_POST['removealbum'], $_POST['removeartist']);
		$album->removeTag($_POST['removetag'], $this_user->uniqueid);
		$nocache=true;
	} else if (isset($_POST['artistremovetag'])) {
		// remove artist tag
		$artist = new Artist($_POST['removeartist']);
		$artist->removeTag($_POST['removetag'], $this_user->uniqueid);
		$nocache=true;
	}
	
	if (isset($_GET['tag'])) {
		// show artists, albums and tracks with this tag
		$page->getTaggedArtists($nocache);
		$page->getTaggedAlbums($nocache);
		$page->getTaggedTracks($nocache);
		
		$smarty->assign('page', $page);
	} else {
		// Tags
		$page->getTags();
		$smarty->assign('page', $page);
	}
	$smarty->display('user-library-tags.tpl');
}
