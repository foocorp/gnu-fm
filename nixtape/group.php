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
require_once('data/Group.php');
require_once('data/TagCloud.php');
require_once('data/Server.php');

if (! $_GET['group'])
{
	print "Here we should provide a list of existing groups, perhaps largest first.";
	exit;
}

$group = new Group($_GET['group']);

if ($_GET['action'] && $_SESSION['user']->name)
{
	if ($_GET['action'] == 'join')
		$group->memberJoin($_SESSION['user']);
	elseif ($_GET['action'] == 'leave')
		$group->memberLeave($_SESSION['user']);
		
	header("Location: " . $group->getURL());
	exit;
}


if(isset($group->name)) {

	$smarty->assign("id", $group->id);
	$smarty->assign('group', $group->name);
	$smarty->assign('fullname', $group->fullname);
	$smarty->assign('bio', $group->bio);
	$smarty->assign('homepage', $group->homepage);
	$smarty->assign('avatar', $group->getAvatar());

	$aUserTagCloud = $group->tagCloudData();
	if (!PEAR::isError ($aUserTagCloud)) {
		$smarty->assign('group_tagcloud',$aUserTagCloud);
	}
	
	$smarty->assign('userlist', $group->getUsers());

	$smarty->assign('ismember', $group->memberCheck($_SESSION['user']));
	$smarty->assign('isowner', ($group->owner->name==$_SESSION['user']->name));
	$smarty->assign('link_join', $group->getURLAction('join'));
	$smarty->assign('link_leave', $group->getURLAction('leave'));
	$smarty->assign('link_edit', $base_url.'/edit_group.php?group='.$group->name);

	$smarty->assign('extra_head_links', array(
			array(
				'rel' => 'meta',
				'type' => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href' => $base_url.'/rdf.php?fmt=xml&page='.htmlentities($_SERVER['REQUEST_URI'])
				)
		));
	
	$smarty->assign('profile', true);
	$smarty->display('group.tpl');

} else {
	$smarty->assign('error', 'Group not found');
	$smarty->assign('details', 'Shall I call in a missing peoples report?');
	$smarty->display('error.tpl');
}

?>
