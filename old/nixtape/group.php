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
require_once('data/Group.php');
require_once('data/TagCloud.php');
require_once('data/Server.php');

?><h1>Groups are disabled</h1><?php

// if (! $_GET['group'])
// {
// 	$smarty->assign('groups', Group::groupList());
// 	$smarty->assign('extra_head_links', array(
// 				array(
// 					'rel' => 'meta',
// 					'type' => 'application/rdf+xml' ,
// 					'title' => 'FOAF',
// 					'href' => $base_url.'/rdf.php?fmt=xml&page='.urlencode(str_replace($base_url, '', $_SERVER['REQUEST_URI']))
// 				     )
// 				));
// 	try {
// 		$aTagCloud = TagCloud::GenerateTagCloud(TagCloud::scrobblesTable(), 'artist');
// 		$smarty->assign('tagcloud', $aTagCloud);
// 	} catch (exception $e) {}
// 	$smarty->display('group-list.tpl');
// 	exit;
// }

// $group = new Group($_GET['group']);

// if ($_GET['action'] && isset($this_user))
// {
// 	if ($_GET['action'] == 'join')
// 		$group->memberJoin($this_user);
// 	elseif ($_GET['action'] == 'leave')
// 		$group->memberLeave($this_user);

// 	header('Location: ' . $group->getURL());
// 	exit;
// }


// if(isset($group->name)) {

// 	$smarty->assign('id', $group->id);
// 	$smarty->assign('group', $group->name);
// 	$smarty->assign('fullname', $group->fullname);
// 	$smarty->assign('bio', $group->bio);
// 	$smarty->assign('homepage', $group->homepage);
// 	$smarty->assign('avatar', $group->getAvatar());

// 	try {
// 		$aUserTagCloud = $group->tagCloudData();
// 		$smarty->assign('group_tagcloud',$aUserTagCloud);
// 	} catch (exception $e) {}

// 	$smarty->assign('userlist', $group->getUsers());

// 	$smarty->assign('ismember', $group->memberCheck($this_user));
// 	$smarty->assign('isowner', ($group->owner->name==$this_user->name));
// 	$smarty->assign('link_join', $group->getURLAction('join'));
// 	$smarty->assign('link_leave', $group->getURLAction('leave'));
// 	$smarty->assign('link_edit', $base_url.'/edit_group.php?group='.$group->name);
// 	$smarty->assign('link', $group->getURL());

// 	$smarty->assign('extra_head_links', array(
// 				array(
// 					'rel' => 'meta',
// 					'type' => 'application/rdf+xml' ,
// 					'title' => 'FOAF',
// 					'href' => $base_url.'/rdf.php?fmt=xml&page='.urlencode(str_replace($base_url, '', $_SERVER['REQUEST_URI']))
// 				     )
// 				));

// 	$smarty->assign('profile', true);
// 	$smarty->display('group.tpl');

// } else {
// 	$smarty->assign('error', 'Group not found');
// 	$smarty->assign('details', 'Shall I call in a missing peoples report?');
// 	$smarty->display('error.tpl');
// }

