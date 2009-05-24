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

require_once('database.php');
require_once('version.php');

// Display things?

$displaythings = True;

if (!isset($config_version) || $config_version != $version) {
	die("Configuration file is out of date. Please delete it (and associated database) and <a href='install.php'>reinstall</a>."); //TODO: Upgrade script for release versions
}

if (isset($_GET['hs']) && isset($_GET['p'])) {
	if (substr($_GET['p'], 0, 3) == "1.2") {
		require_once('submissions/1.2/handshake.php');
	} elseif (substr($_GET['p'], 0, 3) == "1.1") {
		require_once('submissions/1.1/handshake.php');
	}
} else {
	//If we're not handshaking then just display some nice stats

	if ($displaythings) {

	require_once('display.php');

	} 
	else
	 { 
	 echo "<h1>GNUkebox!</h1><p>Please configure your system for " . $_SERVER['SERVER_NAME'] . "</p>"; }

	 }
?>
