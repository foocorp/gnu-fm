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
require_once('data/User.php');
require_once('data/TagCloud.php');
require_once('data/Statistic.php');

if(!isset($_GET['user']) && $logged_in == false) {
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'User not set! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

$user = new User($_GET['user']);

if(isset($user->name)) {

#	$aUserTagCloud =  TagCloud::GenerateTagCloud(TagCloud::scrobblesTable('user'), 'artist', 40, $user->name);
#	if (!PEAR::isError ($aUserTagCloud)) {
#		$smarty->assign('user_tagcloud',$aUserTagCloud);
#	}

	$smarty->assign('stat_barwidth', 320);
try {
	$aUserPlayStat =  Statistic::GeneratePlayStats('Scrobbles', 'artist', 40, $user->name, 300);
		$smarty->assign('user_playstats',$aUserPlayStat);
	} catch (exception $e) {}

try {
	$aUserDayStat =  Statistic::generatePlayByDays('Scrobbles', 40, $user->name, 300);
		$smarty->assign('user_daystats',$aUserDayStat);
	} catch (exception $e) {}

	if(isset($_GET['week'])) {
		$smarty->assign('toptracks', $user->getTopTracks(40), time() - 604800);
	} else {
		$smarty->assign('toptracks', $user->getTopTracks(40));
	}

	$smarty->assign('me', $user);
	$smarty->assign('geo', Server::getLocationDetails($user->location_uri));
	$smarty->assign('isme', ($this_user->name == $user->name));

	$smarty->assign('extra_head_links', array(
			array(
				'rel' => 'meta',
				'type' => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href' => $base_url.'/rdf.php?fmt=xml&page='.urlencode(str_replace($base_url, '', $user->getURL()))
				)
		));

	$smarty->assign('stats', true);
	$smarty->display('user-stats.tpl');
} else {
	$smarty->assign('error', 'User not found');
	$smarty->assign('details', 'Shall I call in a missing persons report?');
	$smarty->display('error.tpl');
}

?>
