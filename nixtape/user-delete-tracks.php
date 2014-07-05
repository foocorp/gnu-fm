<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009,2014 Free Software Foundation, Inc

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
require_once('data/RemoteUser.php');
require_once('data/TagCloud.php');
require_once('data/Server.php');
require_once('data/Library.php');
require_once('utils/human-time.php');

if ($logged_in == false) {
	displayError("Error", "Not logged in. You shouldn't be here.");
}

// For some bizarre reason $this_user->getScrobbles() fails,
// so make a new user object to use instead until this is fixed.
$user = new User($this_user->name);

$errors = array();

// The user submitted zero or more scrobbles to delete. Delete them

if ($_POST['submit']) {
    $deletions = $_POST['scrobble'];
    array_map(function($scrob) {
        global $user, $errors;
        // html_entity_decode defaults to utf8
        list($timestamp,$artist,$track) = array_map('html_entity_decode',
                                                    explode("\t", $scrob));
        // The user id (and SQL ? string in the function's query) mean that
        // you can't delete other users' scrobbles or inject SQL.
        $ok = Library::removeScrobble($user->uniqueid, $timestamp, $artist,
                                      $track);
        if($ok) {
            $errors[] = "Deleted " . $track . " by " . $artist . " from "
                . human_timestamp($timestamp);
        } else {
            $errors[] = " Couldn't delete " . $track . " by " . $artist
                . " from " . human_timestamp($timestamp);
        }
    }, $deletions);
}

// Whether post or get, we've a UI to render. Get and calculate scrobble paging.

$scrobbleCount = (int)$_REQUEST['count'];
if ($scobbleCount >= 1200) {
	$scrobbleCount = 1200;
} else if (!$scrobbleCount) {
	$scrobbleCount = 100;
}

$scrobbleOffset = (int)$_REQUEST['offset'];
$scrobbleOffsetNext = ($scrobbleOffset + $scrobbleCount
                       > $user->getTotalTracks())
                       ? -1 : $scrobbleOffset + $scrobbleCount;
$scrobbleOffsetPrev = ($scrobbleOffset > 0)
                       ? ($scrobbleOffset - $scrobbleCount) : -1;

// Get the current page of scrobbles

try {
	$aUserScrobbles = $user->getScrobbles($scrobbleCount, $scrobbleOffset);
	$smarty->assign('scrobbles', $aUserScrobbles);
} catch (Exception $e) {}

// Assign and render

$smarty->assign('geo', Server::getLocationDetails($user->location_uri));

$smarty->assign('me', $user);
$smarty->assign('pagetitle', $user->name . '\'s tracks for deletion');
$smarty->assign('errors', $errors);

$smarty->assign('scrobbleCount', $scrobbleCount);
$smarty->assign('scrobbleOffset', $scrobbleOffset);
$smarty->assign('nextOffset', $scrobbleOffsetNext);
$smarty->assign('prevOffset', $scrobbleOffsetPrev);

$submenu = user_menu($user, 'Delete Tracks');
$smarty->assign('submenu', $submenu);

$smarty->display('user-delete-tracks.tpl');
