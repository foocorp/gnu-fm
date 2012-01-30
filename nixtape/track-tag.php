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

require_once('database.php');
require_once('templating.php');
require_once('data/TagCloud.php');
require_once('track-menu.php');

if ($logged_in == false) {
	$smarty->assign('pageheading', 'Log in required');
	$smarty->assign('details', 'You need to log in to tag tracks.');
	$smarty->display('error.tpl');
	die();
}

try {
	$track = new Track($_GET['track'], $_GET['artist']);
	$smarty->assign('track', $track);
} catch (Exception $e) {
	//TODO Make track throw exception
	$smarty->assign('pageheading', 'Track not found.');
	$smarty->assign('details', 'The track ' . $_GET['track'] . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

if($_POST['tag']) {
	$track->addTags($_POST['tags'], $this_user->uniqueid);
}

$smarty->assign('pagetitle', $track->artist_name . ' : ' . $track->name);

try {
	$tagCloud = TagCloud::generateTagCloud('Tags', 'tag', 10, $track->name, 'track');
	$smarty->assign('tagcloud', $tagCloud);
} catch(Exception $e) {
	$tagCloud = array();
}

$smarty->assign('mytags', $track->getTags($this_user->uniqueid, null, null, 0));

$submenu = track_menu($track, 'Tag');
$smarty->assign('submenu', $submenu);

$smarty->assign('headerfile', 'track-header.tpl');
$smarty->display('track-tag.tpl');
