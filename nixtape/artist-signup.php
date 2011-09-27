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
require_once('data/sanitize.php');
require_once('data/Server.php');

if ($logged_in == false) {
	$smarty->assign('pageheading', _('Artist Sign-Up'));
	$smarty->display('artist-signup.tpl');
	die();
}


if(isset($_POST['artist_name']) && !empty($_POST['artist_name'])) {
	$already_exists = false;
	$name = trim($_POST['artist_name']);
	try {
		$artist = new Artist($name);
		$already_exists = true;
	} catch (Exception $e) {
	}

	$create = false;
	if($already_exists) {
		$managers = $artist->getManagers();
		$smarty->assign('managers', $managers);
		if(isset($_POST['confirm_artist']) && empty($managers)) {
			if($artist->getListenerCount() > 100) {
				$adodb->Execute("INSERT INTO Manages (userid, artist, authorised) VALUES (" . (int) $this_user->uniqueid . ", "
					. $adodb->qstr($artist->name) . ", 0)");
				$smarty->assign('too_popular', true);
			} else {
				$adodb->Execute("INSERT INTO Manages (userid, artist, authorised) VALUES (" . (int) $this_user->uniqueid . ", "
					. $adodb->qstr($artist->name) . ", 1)");
				$smarty->assign('created', true);
			}
		} elseif(isset($_POST['reject_artist'])) {
			$smarty->assign('reject_artist', true);
		} else {
			$smarty->assign('creating', true);
		}
	} else {
		$adodb->Execute("INSERT INTO Artist (name) VALUES(" . $adodb->qstr($name) . ")");
		$artist = new Artist($name, false, true); // Force recaching since this is a new artist
		$adodb->Execute("INSERT INTO Manages (userid, artist, authorised) VALUES (" . (int) $this_user->uniqueid . ", "
			. $adodb->qstr($artist->name) . ", 1)");
		$smarty->assign('created', true);
	}
	$smarty->assign('artist', $artist);
	$smarty->assign('already_exists', $already_exists);
}

$smarty->assign('pageheading', _('Artist Sign-Up'));
$smarty->display('artist-signup.tpl');
