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

$group = new Group($_GET['group']);

if(isset($group->name)) {

	$smarty->assign("id", $user->id);
	$smarty->assign('group', $user->name);
	$smarty->assign('fullname', $user->fullname);
	$smarty->assign('bio', $user->bio);
	$smarty->assign('homepage', $user->homepage);
	$smarty->assign('avatar', $user->getAvatar());
	$aUserTagCloud = Group::tagCloudData();
	if (!PEAR::isError ($aUserTagCloud)) {
		$smarty->assign('group_tagcloud',$aUserTagCloud);
	}
	$smarty->assign('userlist', $group->getUsers());
	
	$smarty->assign('extra_head_links', array(
			array(
				'rel' => 'meta',
				'type' => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href' => $base_url.'/rdf.php?fmt=xml&page='.htmlentities($_SERVER['REQUEST_URI'])
				)
		));
	
	$smarty->display('group.tpl');

} else {
	$smarty->assign('error', 'Group not found');
	$smarty->assign('details', 'Shall I call in a missing peoples report?');
	$smarty->display('error.tpl');
}

?>
