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
require_once('data/User.php');
require_once('data/TagCloud.php');
require_once('data/Server.php');

if(!isset($_GET['user']) && $logged_in == false) {
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'User not set! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

$user = new User(urldecode($_GET['user']));
$scrobbleCount = (int)$_GET['count'];
if ($scobbleCount >= 1200)
	$scrobbleCount = 1200;
elseif (!$scrobbleCount)
	$scrobbleCount = 100;

if(isset($user->name)) {

	$smarty->assign('geo', Server::getLocationDetails($user->location_uri));
	try {
		$aUserScrobbles = $user->getScrobbles( $scrobbleCount );
		$smarty->assign('scrobbles', $aUserScrobbles);
	} catch (exception $e) {}
	try {
	$aUserTagCloud =  TagCloud::GenerateTagCloud(TagCloud::scrobblesTable('user'), 'artist', 40, $user->uniqueid);
		$smarty->assign('user_tagcloud',$aUserTagCloud);
	} catch (exception $e) {}
	$smarty->assign('isme', ($this_user->name == $user->name));
	$smarty->assign('me', $user);
	$smarty->assign('profile', true);

	$smarty->assign('extra_head_links', array(
			array(
				'rel'=>'alternate',
				'type' => 'application/rss+xml' ,
				'title' => 'RSS 1.0 Feed (Recent plays)',
				'href' => $base_url.'/rdf.php?fmt=rss&page='.urlencode(str_replace($base_url, '', $user->getURL('recent-tracks')))
				),
			array(
				'rel' => 'meta',
				'type' => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href' => $base_url.'/rdf.php?fmt=xml&page='.urlencode(str_replace($base_url, '', $user->getURL()))
				)
		));

	$smarty->display('user-recent-tracks.tpl');
} else {
	$smarty->assign('error', 'User not found');
	$smarty->assign('details', 'Shall I call in a missing persons report?');
	$smarty->display('error.tpl');
}

?>
