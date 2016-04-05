<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2016 Free Software Foundation, Inc

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

require_once('../database.php');
require_once('../data/User.php');
require_once('../data/RemoteUser.php');
//require_once('../data/Server.php');

use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

if (!isset($_GET['user'])) {
	http_response_code(400);
	echo "User not set.\n";
	exit;
}

try {
	if(strstr($_GET['user'], '@')) {
		$user = new RemoteUser($_GET['user']);
	} else {
		$user = new User($_GET['user']);
	}
} catch (Exception $e) {
	http_response_code(400);
	echo "User not found, shall I call in a missing persons report?\n";
	exit;
}

if (isset($_GET['count'])) {
	$scobbleCount = (int)$_GET['count'];
	if ($scobbleCount >= 1200) {
		$scrobbleCount = 1200;
	}
} else {
	$scrobbleCount = 100;
}

try {
	$aUserScrobbles = $user->getScrobbles($scrobbleCount);
} catch (Exception $e) {
	http_response_code(400);
	echo "Couldn't get scrobbles.\n";
	exit;
}

$feed = new Feed();
$channel = new Channel();

$channel
	->title($user->name . '\'s recent tracks')
	->description('RSS 2.0 Feed (Recent plays)')
	->url($user->getURL())
	->language('en')
	->pubDate(time())
	->ttl(60)
	->appendTo($feed);

foreach ($aUserScrobbles as $scrobble) {
	$item = new Item();
	$item
		->title($scrobble['track'])
		->url($scrobble['trackurl'])
		->pubDate($scrobble['time'])
		->guid($scrobble['id'])
		->description($scrobble['artisturl'])
		->appendTo($channel);
}

echo $feed;
