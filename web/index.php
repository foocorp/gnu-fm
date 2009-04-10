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

$res = $mdb2->query('SELECT username, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 10');

if(PEAR::isError($res)) {
  die($res->getMessage());
}

$smarty->assign('recenttracks', $res->fetchAll(MDB2_FETCHMODE_ASSOC));

$smarty->display('welcome.tpl');
?>
