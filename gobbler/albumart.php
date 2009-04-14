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

header('Content-type: text/html; charset=utf-8');
require_once('database.php');

			$res = $mdb2->query("SELECT name, artist_name from Album order by artist_name");

			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			$i = 0;
			while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$i++;
				foreach($row as $field => $value) {
					if($field == "name"){
					  echo $value;
					}  
				}
			}
?>
</body>
</html>

