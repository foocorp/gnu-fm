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

if(!isset($_GET['tag'])) {
        $smarty->assign('pageheading', 'No tag specified');
        $smarty->assign('details', 'You need to specify what tag you wish to view details for.');
	$smarty->display('error.tpl');
	die();
}

$tag = $_GET['tag'];
$smarty->assign('tag', $tag);

try {
	$tagCloud = TagCloud::generateTagCloud('tags', 'artist', 40, $tag, "tag");
	$smarty->assign('tagcloud', $tagCloud);
} catch (exception $ex) {
	$smarty->assign('pageheading', 'No artists found');
	$smarty->assign('details', 'No artists could be found that have been tagged with "' . $tag . '"');
	$smarty->display('error.tpl');
	die();
}

$smarty->display("tag.tpl");
