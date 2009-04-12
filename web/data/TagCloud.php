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

class TagCloud {
   /*
    * returns an array counting appareances of a given field and his corresponding font-size.
    * @param string $table table name to be queried
    * @param string $field field name to count
    * @param integer $limit limit of the query
    * @param integer $sizes quantity of possible sizes
    * @param float $max_font_size maximum font size (px, em, %, etc)
    * @return array tagcloud
    */
    #function __construct($table, $field, $limit = 40, $sizes = 6, $max_font_size = 3, $username = null) {
    function __construct($table, $field, $limit = 40, $username = null) {
        global $mdb2;

        if (!is_string($field))          return false;
        if (!is_string($table))          return false;
        if (!is_integer($limit))         return false;
      # if (!is_integer($sizes))         return false;
      # if (!is_numeric($max_font_size)) return false;

        $query = "SELECT $field, count(*) AS count FROM $table";
        $query .= (!is_null($username)) ? ' WHERE username = ' . $mdb2->quote($username, 'text') : null;
        $query .= " GROUP BY $field ORDER BY count DESC LIMIT $limit";

        $res = $mdb2->query($query);
        
        if (!$res->numRows()) {
            return false;
        } else {
            $this->tagcloud = $res->FetchAll(MDB2_FETCHMODE_ASSOC); 

            $this->min = end($this->tagcloud);
            $this->max = reset($this->tagcloud);

            # scramble results
            shuffle($this->tagcloud);

            # creates a range of possible font sizes
            # $range_of_sizes = range(0, $max_font_size, round($max_font_size / $sizes));
            $range_of_sizes = array('xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large');

            foreach ($this->tagcloud as $row => &$data) {
                # gets a size from range_of_sizes
                $data['size'] = $range_of_sizes[floor(((count($range_of_sizes) - 1) * $data['count']) / $this->max['count'])];
                $data[$field] = stripslashes($data[$field]);
            }

            $res = null; 
            unset($res);
        }
    }

    function __destruct() {
       unset($this->max, $this->min, $range_of_sizes, $data, $this->tagcloud); 
    }
}
?>
