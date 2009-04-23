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
require_once($install_path . '/data/Server.php');

class PlayStats {
   /*
    * returns an array counting appareances of a given field and his corresponding bargraph size
    * @param string $table table name to be queried
    * @param string $field field name to count
    * @param integer $limit limit of the query
    * @param string $constraint username or artistname depending on field
    * @param integer $maxwidth bargraph max width ( to express visually the number of plays )
    * inaccurate @param integer $sizes quantity of possible sizes
    * inaccurate @param float $max_font_size maximum font size (px, em, %, etc)
    * @return array playstats
    */
    static function generatePlayStats($table, $field, $limit = 40, $constraint = null, $maxwidth = 100 ) {
        global $mdb2;
        if (!is_string($field))          return false;	
        if (!is_string($table))          return false;
        if (!is_integer($limit))         return false;
    	$sizes = array('xx-large', 'x-large', 'large', 'medium', 'small', 'x-small', 'xx-small');
        $query = "SELECT $field, count(*) AS count FROM $table";
        $query .= (!is_null($constraint) || ($table == "Scrobbles")) ? ' WHERE ' : null;
	if ($field == "track") {
        $query .= (!is_null($constraint)) ? ' artist = ' . $mdb2->quote($constraint, 'text') : null;
	} else {
        $query .= (!is_null($constraint)) ? ' username = ' . $mdb2->quote($constraint, 'text') : null;
	}
        $query .= (!is_null($constraint) && ($table == "Scrobbles")) ? ' AND ' : null;
        $query .= ($table == "Scrobbles") ? " rating <> 'S' " : null;
        $query .= " GROUP BY $field ORDER BY count DESC LIMIT $limit";
        $res = $mdb2->query($query);
	if (PEAR::isError($res)) {
        	echo("ERROR" . $res->getMessage());
	}

        if (!$res->numRows()) {
        	return false;
        } else {
                $data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
                $max = $data[0]['count'];
                
		        foreach($data as &$i){
					$i['pageurl'] = Server::getArtistURL($i['artist']);
					$i['size'] = $i['count'] / $max * $maxwidth;
                }
                
                return $data;
        }
    }
}
?>
