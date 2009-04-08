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


require_once($install_path . '/database.php');
require_once($install_path . "/data/sanitize.php");

/**
 * Represents User data
 *
 * General attributes are accessible as public variables.
 *
 */
class User {


        public $name, $email, $fullname, $bio, $location, $homepage, $error;

        /**
         * Artist constructor
         *
         * @param string name The name of the artist to load
         */
        function __construct($name) {
                global $mdb2;
                $res = $mdb2->query("SELECT * FROM Users WHERE "
                        . "username = " . $mdb2->quote($name, "text"));
                if($res->numRows()) {
                        $row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
                        $this->name = $row["username"];
                        $this->email = $row["email"];
                        $this->fullname = $row["fullname"];
                        $this->homepage = $row["homepage"];
                        $this->bio = $row["bio"];
                        $this->location = $row["location"];
                }
        }
	function getgobbles($number) {	
		$res = $mdb2->query("SELECT "); /* stub for now */
		
        }


}

