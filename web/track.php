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


require_once("database.php");
require_once("templating.php");
require_once("data/Track.php");

$track = new Track($_GET['track'], $_GET['artist']);

$smarty->assign("name", $track->name);
$smarty->assign("artist", $track->artist_name);
$smarty->assign("album", $track->album_name);
$smarty->assign("duration", $track->duration);
$smarty->assign("license", $track->license);
$smarty->assign("playcount", $track->getPlayCount());
$smarty->assign("listeners", $track->getListenerCount());

$smarty->display("track.tpl");
?>
