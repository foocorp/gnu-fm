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

if ($logged_in == false) {
	$smarty->assign('pageheading', 'Error!');
	$smarty->assign('details', 'Not logged in! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

$errors = array();

if (isset($_POST['submit'])) {
	$old_password = md5($_POST['old_password']);
	if ($old_password != $this_user->password) {
		$errors[] = 'Old password did not match the one associated with your profile.';
	}

	if ($_POST['password1'] != $_POST['password2']) {
		$errors[] = 'Passwords do not match!';
	}

	if (!isset($errors[0])) {
		$this_user->password = md5($_POST['password1']);
		$this_user->save();
		$smarty->assign('success', true);
	}
}
$smarty->assign('errors', $errors);
$smarty->display('user-change-password.tpl');
