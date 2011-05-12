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
require_once('user-menu.php');
require_once('data/User.php');
require_once('data/TagCloud.php');

if ($logged_in == false) {
	$smarty->assign('pageheading', 'Error!');
	$smarty->assign('details', 'Not logged in! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

if (isset($_GET['token']) && isset($_GET['webservice_url'])) {
	// Handle authentication callback from a foreign service
	$token = $_GET['token'];
	$webservice_url = $_GET['webservice_url'];
	$sig = md5('api_key' . $lastfm_key . 'methodauth.getSession' . 'token' . $token . $lastfm_secret);
	$xmlresponse = simplexml_load_file($webservice_url . '?method=auth.getSession&token=' . $token . '&api_key=' . $lastfm_key . '&api_sig=' . $sig);
	foreach ($xmlresponse->children() as $child => $value) {
		if ($child == 'session') {
			foreach ($value->children() as $child2 => $value2) {
				if ($child2 == 'name') {
					$remote_username = $value2;
				} else if ($child2 == 'key') {
					$remote_key = $value2;
				}
			}
		}
	}

	if (!isset($remote_username) || !isset($remote_key)) {
		$smarty->assign('pageheading', 'Error!');
		$smarty->assign('details', 'Sorry, we weren\'t able to authenticate your account.');
		$smarty->display('error.tpl');
		die();
	}

	// Delete any old connection to this service
	$adodb->Execute('DELETE FROM Service_Connections WHERE '
		. 'userid = ' . $this_user->uniqueid . ' AND '
		. 'webservice_url = ' . $adodb->qstr($webservice_url));

	// Create our new connection
	$adodb->Execute('INSERT INTO Service_Connections VALUES('
		. $this_user->uniqueid . ', '
		. $adodb->qstr($webservice_url) . ', '
		. $adodb->qstr($remote_key) . ', '
		. $adodb->qstr($remote_username) . ')');

	// Flush cache so this change takes effect immediately
	$adodb->CacheFlush('SELECT * FROM Service_Connections WHERE userid = ' . $this_user->uniqueid . ' AND forward = 1');

	$smarty->assign('connection_added', true);
}

if (isset($_GET['forward']) && isset($_GET['service'])) {
	// Update the user's forwarding preferences
	$adodb->Execute('UPDATE Service_Connections SET forward = ' . (int) ($_GET['forward'])
		. ' WHERE userid = ' . $this_user->uniqueid
		. ' AND webservice_url = ' . $adodb->qstr($_GET['service']));

	// Flush cache so this change takes effect immediately
	$adodb->CacheFlush('SELECT * FROM Service_Connections WHERE userid = ' . $this_user->uniqueid . ' AND forward = 1');
}

if (isset($lastfm_key)) {
	$smarty->assign('lastfm_key', $lastfm_key);
}

$smarty->assign('connections', $this_user->getConnections());

$submenu = user_menu($this_user, 'Edit');
$smarty->assign('submenu', $submenu);

$smarty->assign('me', $this_user);
$smarty->assign('headerfile', 'maxiprofile.tpl');
$smarty->display('user-connections.tpl');
