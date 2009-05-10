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

require("../../config.php");
require_once($install_path . '/database2.php');
require_once($install_path . '/templating.php');
require_once($install_path . '/data/sanitize.php');

$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
$recordSet = &$adodb->CacheExecute(7200, 'SELECT t.id, t.artist_name, t.album_name, t.name, t.mbid as tmbid, st.mbid as stmbid FROM Scrobble_Track st JOIN Track t ON lower(t.name)=st.name AND lower(t.album_name)=st.album AND lower(t.artist_name)=st.artist AND t.mbid<>st.mbid');

$aEntries = array();
$i = 0;

while (!$recordSet->EOF) {
	$trow = sanitize($recordSet->fields);
	$aEntries[$i++] = $trow;
	$recordSet->MoveNext();
}

$smarty->assign("entries", $aEntries);

$smarty->display("mbid-mismatch-report.tpl");
?>
