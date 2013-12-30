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
require_once('user-menu.php');
require_once('data/User.php');
require_once('data/RemoteUser.php');
require_once('data/TagCloud.php');
require_once('data/Statistic.php');
require_once('data/GraphTypes.php');

if (!isset($_GET['user']) && $logged_in == false) {
	displayError("Error", "User not set. You shouldn't be here.");
}

try {
	if(strstr($_GET['user'], '@')) {
		$user = new RemoteUser($_GET['user']);
	} else {
		$user = new User($_GET['user']);
	}
} catch (Exception $e) {
	if ($e->getCode() == 22) {
		$error = "We had some trouble locating that user.  Are you sure you spelled it correctly?";
	} else {
		$error = $e->getMessage();
	}
	$user = null;
}

$toptracks = intval($_GET['toptracks']);
if (!($toptracks >= 10 && $toptracks <= 500)) {
	$toptracks = 20;
}

$topartists = intval($_GET['topartists']);
if (!($topartists >= 10 && $topartists <= 500)) {
	$topartists = 20;
}

if (isset($user->name)) {
	$begin = null;
	$total_tracks_limit = 15000;
	$total_tracks = $user->getTotalTracks();
	if(!$total_tracks) {
		displayError("No stats for user",
			"User {$user->name} doesn't seem to have scrobbled anything yet.");
	}

	// Limit stats to timeperiod if track count is higher than limit
	if($total_tracks > $total_tracks_limit) {
		$begin = strtotime('-6 months');
		$smarty->assign('timeperiod', '(last 6 months)');
	}

	$smarty->assign('stat_barwidth', 320);
	$smarty->assign('topartistspx', 25 * $topartists);
	try {
		$smarty->assign('graphtopartists', new GraphTopArtists($user, $topartists, $begin));
	} catch (exception $e) {}

	try {
		$smarty->assign('graphplaysbydays', new GraphPlaysByDays($user, 20));
	} catch (exception $e) {}

	$smarty->assign('toptrackspx', 25 * $toptracks);
	try {
		$smarty->assign('graphtoptracks', new GraphTopTracks($user, $toptracks, $begin));
	} catch (exception $e) {}

	$smarty->assign('totaltracks', $total_tracks);
	
	$smarty->assign('me', $user);
	$smarty->assign('geo', Server::getLocationDetails($user->location_uri));
	$smarty->assign('isme', ($this_user->name == $user->name));
	$smarty->assign('pagetitle', $user->name . '\'s stats');

	$smarty->assign('extra_head_links', array(
			array(
				'rel'   => 'meta',
				'type'  => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href'  => $base_url . '/rdf.php?fmt=xml&page=' . rawurlencode(str_replace($base_url, '', $user->getURL()))
				),
			array(
				'rel'   => 'stylesheet',
				'type'	=> 'text/css',
				'title' => 'jqPlot CSS',
				'href' 	=> $base_url . '/themes/' . $default_theme . '/css/jquery.jqplot.css'
			)
		));
	
	$submenu = user_menu($user, 'Stats');
	$smarty->assign('submenu', $submenu);

	$smarty->assign('stats', true);
	$smarty->display('user-stats.tpl');
} else {
	displayError("User not found", "User not found, shall I call in a missing persons report?");
}
