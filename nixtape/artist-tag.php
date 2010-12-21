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
require_once('artist-menu.php');

if($logged_in == false) {
	$smarty->assign('pageheading', 'Log in required');
	$smarty->assign('details', 'You need to log in to tag artists.');
	$smarty->display('error.tpl');
	die();
}

try {
	$artist = new Artist(urldecode($_GET['artist']));
} catch (exception $e) {
        $smarty->assign('pageheading', 'Artist not found.');
        $smarty->assign('details', 'The artist '.($_GET['artist']).' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

if ($_POST['tag']) {
	$artist->addTags($_POST['tags'], $this_user->uniqueid);
}

$smarty->assign('name', $artist->name);
$smarty->assign('pagetitle', $artist->name);

try {  
	$tagCloud = TagCloud::generateTagCloud('tags', 'tag', 10, $artist->name, "artist");
	$smarty->assign('tagcloud', $tagCloud);
} catch (exception $ex) {
	$tagCloud = array();
}

$smarty->assign('mytags', $this_user->getTagsForArtist($artist->name));

$submenu = artist_menu($artist, 'Tag');
$smarty->assign('submenu', $submenu);

$smarty->assign('headerfile', 'artist-header.tpl');
$smarty->display("artist-tag.tpl");
?>
