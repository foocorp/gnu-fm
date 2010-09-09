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
require_once('user-menu.php');
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

try {
	$user = new User(urldecode($_GET['user']));
} catch (exception $e) {
	$error = 'User not found';
}

if(isset($user->name)) {
	$smarty->assign('me', $user);
	$smarty->assign('pagetitle', $user->name . '\'s Radio Station');

	$station = 'librefm://user/' . $user->name . '/loved';
	if(isset($this_user)) {
		        $radio_session = $this_user->getRadioSession($station);
	} else {
		        $radio_session = Server::getRadioSession($station);
	}
	$smarty->assign('radio_session', $radio_session);

	$submenu = user_menu($user, 'Radio Station');
	$smarty->assign('submenu', $submenu);
	$smarty->assign('headerfile', 'maxiprofile.tpl');

	$smarty->display('user-station.tpl');
} else {
	$smarty->assign('error', $error);
	$smarty->assign('details', 'Shall I call in a missing persons report?');
	$smarty->display('error.tpl');
}

?>
