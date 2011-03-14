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

	/*
	 * Humanized timestamps
	 */

	function human_timestamp ($unix_timestamp, $now = null) {
		if (is_null($now)) {
			$now = time();
		}

		$diff = $now - $unix_timestamp;

		# ugly!
		$number_to_alpha = array(
			'zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
			'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty',
			'twenty-one', 'twenty-two', 'twenty-three', 'twenty-four', 'twenty-six', 'twenty-seven', 'twenty-eight', 'twenty-nine', 'thirty',
			'thirty-one', 'thirty-two', 'thirty-three', 'thirty-four', 'thirty-five', 'thirty-six', 'thirty-seven', 'thirty-eight', 'thirty-nine',
			'fourty-one', 'fourty-two', 'fourty-three', 'fourty-four', 'fourty-five', 'fourty-six', 'fourty-seven', 'fourty-eight', 'fourty-nine',
			'fifty-one', 'fifty-two', 'fifty-three', 'fifty-four', 'fifty-five', 'fifty-six', 'fifty-seven', 'fifty-eight', 'fifty-nine'
		);

		switch ($unix_timestamp) {
			case ($now < $unix_timestamp):
				return 'in the future (?)';
				break;
			case ($diff == 1):
				# one second
				return 'a second ago';
				break;
			case ($diff < 60):
				# less than a minute
				return $diff . ' seconds ago';
				break;
			case ($diff < 120):
				# between a minute and two
				return 'about a minute ago';
				break;
			case ($diff < 3600):
				# less than an hour
				return round($diff / 60) . ' minutes ago';
				break;
			case ($diff < 7200):
				# between an hour and two
				return 'about an hour ago';
				break;
			case ($diff < 86400):
				# less than a day
				return round($diff / 3600) . ' hours ago';
				break;
			case ($diff < 172800):
				# less than two days
				return 'about a day ago';
				break;
			case ($diff < 604800):
				# less than a week
				if (round($diff / 86400) == 7)
					return 'about a week ago';
				return round($diff / 86400) . ' days ago';
				break;
			case ($diff < 691200):
				# a week an a day
				return 'about a week ago';
				break;
			case ($diff < 2764800):
				# less than a month
				if (round($diff / 691200) == 1)
					return 'about a week ago';
				return round($diff / 691200) . ' weeks ago';
				break;
			case ($diff < 4579200):
				# a month and three weeks
				return 'about a month ago';
				break;
			case ($diff < 33177600);
				# less than a year
				return round($diff / 2764800) . ' months ago';
				break;
			case ($diff < 35942400):
				# a year and a month
				return 'about a year ago';
				break;
			case ($diff > 35942400):
				return 'more than a year ago';
				break;
		}
	}
?>
