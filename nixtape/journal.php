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
require_once('utils/arc/ARC2.php');
require_once('utils/human-time.php');

if(!isset($_GET['user']) && $logged_in == false) {
        $smarty->assign('error', 'Error!');
        $smarty->assign('details', 'User not set! You shouldn\'t be here!');
        $smarty->display('error.tpl');
        die();
}

$user = new User($_GET['user']);

# We need to get this from account profile really. This is just hard-coded for experimenting.
if ($user->name == 'tobyink')
	$rssFeed = 'http://identi.ca/tobyink/rss';

if ($rssFeed)
{
	# We have to implement HTTP caching here!
	$parser = ARC2::getRDFParser();
	$parser->parse($rssFeed);

	$index = $parser->getSimpleIndex();
	krsort($index); // Newest last.
	$items = array();
	foreach ($index as $subject => $data)
	{
		if (in_array('http://purl.org/rss/1.0/item', $data['http://www.w3.org/1999/02/22-rdf-syntax-ns#type']))
		{
			$ts = strtotime($data[ 'http://purl.org/dc/elements/1.1/date' ][0]);
			$items[] = array(
				'subject_uri' => $subject,
				'title' => $data[ 'http://purl.org/rss/1.0/title' ][0],
				'link' => $data[ 'http://purl.org/rss/1.0/link' ][0],
				'date_iso' => $data[ 'http://purl.org/dc/elements/1.1/date' ][0],
				'date_unix' => $ts,
				'date_human' => human_timestamp($ts)
				);
		}
	}

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
	$smarty->assign('userlevel', $user->userlevel);
	$smarty->assign('avatar', $user->getAvatar());
	$aUserTagCloud =  TagCloud::GenerateTagCloud('Scrobbles', 'artist', 40, $user->name);
	if (!PEAR::isError ($aUserTagCloud)) {
		$smarty->assign('user_tagcloud',$aUserTagCloud);
	}
	$smarty->assign('isme', ($_SESSION['user']->name == $user->name));
	$smarty->assign('profile', true);

	$smarty->assign('items', $items);
	$smarty->display('journal.tpl');
	
} else {
	$smarty->assign('error', 'No RSS Feed for this User');
	$smarty->assign('details', 'Shall I call in a missing feeds report?');
	$smarty->display('error.tpl');
}
