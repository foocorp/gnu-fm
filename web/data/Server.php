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
require_once($install_path . '/data/sanitize.php');
require_once($install_path . '/utils/human-time.php');

/**
 * Represents Server-wide data
 *
 */
class Server {

	public $nowplaying;

        /**
         * Server constructor
         *
         * @param string $name The name of the user to load
         */
        function __construct() {
                global $mdb2;
	    $res = $mdb2->query('SELECT username, artist, track, client, ClientCodes.name, ClientCodes.url from Now_Playing LEFT OUTER JOIN Scrobble_Sessions ON Now_Playing.sessionid=Scrobble_Sessions.sessionid LEFT OUTER JOIN ClientCodes ON Scrobble_Sessions.client=ClientCodes.code ORDER BY time DESC');
            $data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
            foreach($data as &$i) {
                $i = sanitize($i);
                if($i["name"] == "") {
		    $clientstr = strip_tags(stripslashes($i["client"])) . "(unknown, please tell us what this is)";
                } else {
		    $clientstr = "<a href=\"" . strip_tags(stripslashes($i["url"])) . "\">" . strip_tags(stripslashes($i["name"])) . "</a>";
		}

		$i["clientstr"] = $clientstr;
            }

            $this->nowplaying = $data;
	}

}
