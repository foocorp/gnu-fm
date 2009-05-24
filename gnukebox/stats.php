<?php

/* GNUkebox -- a free software server for recording your listening habits

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

header('Content-type: text/html; charset=utf-8');
require_once('database.php');


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Statistics</title>
</head>
<body>
		<h1>Statistics</h1>

		<?php
			$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
			$total = $adodb->CacheGetOne(60, 'SELECT COUNT(*) as total from Scrobbles');
			if(!$res) {
				die("sql error");
			}
			echo "<p>" . stripslashes($total) . " listens.</p>";

			$total = $adodb->CacheGetOne(120, 'SELECT COUNT(*) as total from Track');
			if(!$res) {
				die("sql error");
			}
			echo "<p>" . stripslashes($total) . " unique tracks.</p>";

			$total = $adodb->CacheGetOne(720, 'SELECT COUNT(*) as total from Users');
			if(!$res) {
				die("sql error");
			}
			echo "<p>" . stripslashes($total) . " users.</p>";

		?>
</body>
</html>