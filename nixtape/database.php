<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009, 2015 Free Software Foundation, Inc

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


if (!file_exists(dirname(__FILE__) . '/config.php')) {
	die('Please run the <a href="install.php">Install</a> script to configure your installation');
}

require_once('config.php');

try {
	$adodb =& NewADOConnection($connect_string);
} catch (Exception $e) {
	die("Unable to connect to database.");
}

/**
 * Write error to Error database table
 *
 * @param string msg Message
 * @param string data Data
 * @return null
 */
function reportError($msg, $data) {
	global $adodb;

	$adodb->Execute('INSERT INTO Error(msg, data, time) VALUES('
		. $adodb->qstr($msg) . ', '
		. $adodb->qstr($data) . ', '
		. time() . ')');
}
