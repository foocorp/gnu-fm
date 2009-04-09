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

session_start();

if(isset($_SESSION['session_id'])) {
	$res = $mdb2->query("SELECT username FROM Scrobble_Sessions WHERE "
		. "sessionid = " . $mdb2->quote($_SESSION['session_id'], "text")
	       	. " AND expires > " . time());
	if(!$res->numRows()) {
		// Session is invalid
		unset($_SESSION['session_id']);
	} else {
		$logged_in = true;
		$username = $res->fetchOne(0);
	}
}
?>
