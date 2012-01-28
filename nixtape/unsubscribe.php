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

if (isset($_GET['id']) && strlen($_GET['id']) == 32) {
	$res = $adodb->GetAll('SELECT username FROM Users WHERE MD5(MD5(email) || MD5(password)) = ' . $adodb->qstr($_GET['id']));
	if(count($res) == 0) {
		$smarty->assign('error', true);
	}
	foreach($res as &$row) {
		$user = new User($row['username']);
		$user->receive_emails = 0;
		$user->save();
	}
} else {
	$smarty->assign('error', true);
}

$smarty->assign('pageheading', _('Unsubscribe'));

$smarty->assign('errors', $errors);
$smarty->display('unsubscribe.tpl');
