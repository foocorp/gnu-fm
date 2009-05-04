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
require_once('data/sanitize.php');

$res = $mdb2->query("SELECT t.id, t.artist, t.album, t.name, t.mbid as tmbid, st.mbid as stmbid FROM Scrobble_Track st JOIN Track t ON lower(t.name)=st.name AND lower(t.album)=st.album AND lower(t.artist)=st.artist AND t.mbid<>st.mbid");

$aEntries = array();
$i = 0;

while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
	$trow = sanitize($row);
	$aEntries[$i++] = $row;
}

$smarty->assign("entries", $aEntries);

$smarty->display("mbid-mismatch-report.tpl");
?>
