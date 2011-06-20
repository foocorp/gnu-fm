<?php
/* GNU FM -- a free network service for sharing your music listening habits

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

require_once($install_path . '/database.php');
require_once($install_path . '/data/Server.php');

class Statistic {

	/*
	 * returns an array counting appareances of a given field and his corresponding bargraph size
	 * @param string $table table name to be queried
	 * @param string $field field name to count
	 * @param integer $limit limit of the query
	 * @param string $constraint username or artistname depending on field
	 * @param integer $maxwidth bargraph max width (to express visually the number of plays)
	 * inaccurate @param integer $sizes quantity of possible sizes
	 * inaccurate @param float $max_font_size maximum font size (px, em, %, etc)
	 * @return array playstats
	 */
	static function generatePlayStats($table, $field, $limit = 40, $constraint = null, $maxwidth = 100) {
		global $adodb;
		if (!is_string($field) || !is_string($table) || !is_integer($limit)) {
			return false;
		}
		$query = 'SELECT ' . $field . ', count(*) AS count FROM ' . $table;
		$query .= (!is_null($constraint)) ? ' WHERE ' : null;
		if ($field == 'track') {
			$query .= (!is_null($constraint)) ? ' artist = ' . $adodb->qstr($constraint) : null;
		} else {
			$query .= (!is_null($constraint)) ? ' userid = ' . ($constraint) : null;
		}
		$query .= ' GROUP BY ' . $field . ' ORDER BY count DESC LIMIT ' . $limit;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$res = $adodb->GetAll($query);
		} catch (Exception $e) {
			echo('ERROR' . $e->getMessage());
		}
		if (!$res) {
			return false;
		} else {
			$max = $res[0]['count'];

			foreach ($res as &$i){
				$i['pageurl'] = Server::getArtistURL($i['artist']);
				$i['size'] = round($i['count'] / $max * $maxwidth);
			}

			return $res;
		}
	}

	static function generatePlayByDays($table, $limit = 100, $constraint = null, $maxwidth = 100) {
		global $adodb;
		global $connect_string;

		if (!is_string($table) || !is_integer($limit)) {
			return false;
		}

		/*
		 * todo: completly remove this dirty db type check.
		 */
		$query = 'SELECT COUNT(*) as count, DATE(TO_TIMESTAMP(time)) as date FROM ' . $table;
		if (strpos($connect_string, 'mysql') === 0) {
			$query = 'SELECT COUNT(*) as count,DATE(FROM_UNIXTIME(time)) as date FROM ' . $table;
		}

		$query .= (!is_null($constraint)) ? ' WHERE ' : null;
		$query .= (!is_null($constraint)) ? ' userid = ' . ($constraint) : null;
		$query .= ' GROUP BY date ORDER BY date DESC LIMIT ' . $limit;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
			$res = $adodb->GetAll($query);
		} catch (Exception $e) {
			echo('ERROR' . $e->getMessage());
		}
		if (!$res) {
			return false;
		} else {
			$max = 0;

			foreach ($res as &$i){
				if ($i['count'] > $max) {
					$max = $i['count'];
				}
			}

			return $res;
		}
	}
}
