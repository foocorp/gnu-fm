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
require_once('data/Server.php');

if(!isset($_GET['user']) && $logged_in == false) {
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'User not set! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

$user = new User($_GET['user']);

if(isset($user->name)) {
	$smarty->assign("id", $user->id);
	$smarty->assign("acctid", $user->acctid);
	$smarty->assign('user', $user->name);
	$smarty->assign('email', $user->email);
	$smarty->assign('fullname', $user->fullname);
	$smarty->assign('bio', $user->bio);
	$smarty->assign('homepage', $user->homepage);
	$smarty->assign('location', $user->location);
	$smarty->assign('location_uri', $user->location_uri);
	$smarty->assign('geo', Server::getLocationDetails($user->location_uri));
	$aUserScrobbles = $user->getScrobbles(20);
	if (!PEAR::isError ($aUserScrobbles)) {
		$smarty->assign('scrobbles', $aUserScrobbles);
	}
	$smarty->assign('userlevel', $user->userlevel);
	$smarty->assign('avatar', $user->getAvatar());
	$aUserNowPlaying = $user->getNowPlaying(10);
	if (!PEAR::isError ($aUserNowPlaying)) {
		$smarty->assign('nowplaying', $aUserNowPlaying);
	}
	$aUserTagCloud =  TagCloud::GenerateTagCloud('Scrobbles', 'artist', 40, $user->name);
	if (!PEAR::isError ($aUserTagCloud)) {
		$smarty->assign('user_tagcloud',$aUserTagCloud);
	}
	$smarty->assign('isme', ($_SESSION['user']->name == $user->name));
	$smarty->assign('profile', true);
	
	$smarty->assign('extra_head_links', array(
			array(
				'rel'=>'alternate',
				'type' => 'application/rss+xml' ,
				'title' => 'RSS 1.0 Feed (Recent plays)',
				'href' => $base_url.'/rdf.php?fmt=rss&page='.htmlentities($_SERVER['REQUEST_URI'])
				),
			array(
				'rel' => 'meta',
				'type' => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href' => $base_url.'/rdf.php?fmt=xml&page='.htmlentities($_SERVER['REQUEST_URI'])
				)
		));

	$smarty->assign('toptracks', $user->getTopTracks);
	
	$smarty->display('profile.tpl');
} else {
	$smarty->assign('error', 'User not found');
	$smarty->assign('details', 'Shall I call in a missing persons report?');
	$smarty->display('error.tpl');
}

?>
