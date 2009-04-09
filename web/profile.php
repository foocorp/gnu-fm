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
require_once('utils/EmailAddressValidator.php');

if(!isset($_GET['user'])) {
	$smarty->assign("error", "Error!");
	$smarty->assign("details", "User not set! You shouldn't be here!");
	$smarty->display("error.tpl");
	die();
}

$user = new User($_GET['user']);
if(isset($user->name)) { 
	$smarty->assign("user", $user->name);
	$smarty->assign("email", $user->email);
	$smarty->assign("fullname", $user->fullname);
	$smarty->assign("bio", $user->bio);
	$smarty->assign("homepage", $user->homepage);
	$smarty->assign("location", $user->location);
	$smarty->assign("scrobbles", $user->getscrobbles(10));
	$smarty->display("profile.tpl");
} else {
	$smarty->assign("error", "User not found");
	$smarty->assign("details", "Shall I call in a missing persons report?");
	$smarty->display("error.tpl");
}
?>
