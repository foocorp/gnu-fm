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
	header("Content-Type: text/plain");
	print_r($group);
	print_r($group->getUsers());
	print_r($group->tagCloudData());
} else {
	$smarty->assign('error', 'Group not found');
	$smarty->assign('details', 'Shall I call in a missing peoples report?');
	$smarty->display('error.tpl');
}

?>
