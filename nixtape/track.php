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
require_once('track-menu.php');

$smarty->assign('flattr_uid', $artist->flattr_uid);
$smarty->assign('url', $track->getURL());

if ($track->duration) {
	// Give the duration in MM:SS
	$mins = floor($track->duration / 60);
	$sec = floor($track->duration % 60);
	if (strlen($sec) == 1) {
		$sec = '0' . $sec;
	}
	$duration = $mins . ':' . $sec;
	$smarty->assign('duration', $duration);
}

$smarty->assign('extra_head_links', array(
		array(
			'rel'   => 'meta',
			'type'  => 'application/rdf+xml',
			'title' => 'Track Metadata',
			'href'  => $base_url . '/rdf.php?fmt=xml&page=' . rawurlencode(str_replace($base_url, '', $track->getURL()))
			)
		));

try {
	$tagCloud = TagCloud::generateTagCloud('tags', 'tag', 10, 'track', array($track->name, $track->artist_name));
} catch ( Exception $e) {
	$tagCloud = array();
}
$smarty->assign('tagcloud', $tagCloud);

if ($logged_in) {
	if($_POST['love']) {
		$track->love($this_user->uniqueid);
	}
	if($_POST['unlove']) {
		$track->unlove($this_user->uniqueid);
	}
	$smarty->assign('isloved', $track->isLoved($this_user->uniqueid));
}
	
$submenu = track_menu($track, 'Overview');
$smarty->assign('submenu', $submenu);
$smarty->display('track.tpl');
