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

<?

if(!file_exists(dirname(__FILE__) . "/config.php")) {
	die("Please run the <a href='install.php'>Install</a> script to configure your installation");
}

require_once('config.php');
require_once('MDB2.php');

$mdb2 =& MDB2::connect($connect_string);
if (PEAR::isError($mdb2)) {
	die($mdb2->getMessage());
}

?>
