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

	switch ($unix_timestamp) {
		case ($now < $unix_timestamp):
			return _('in the future (?)');
			break;
		case ($diff == 1):
			# one second
			return _('a second ago');
			break;
		case ($diff < 60):
			# less than a minute
			return sprintf('%d seconds ago', $diff);
			break;
		case ($diff < 120):
			# between a minute and two
			return _('about a minute ago');
			break;
		case ($diff < 3600):
			# less than an hour
			return sprintf(_('%d minutes ago'), round($diff / 60));
			break;
		case ($diff < 7200):
			# between an hour and two
			return _('about an hour ago');
			break;
		case ($diff < 86400):
			# less than a day
			return sprintf(_('%d hours ago'), round($diff / 3600));
			break;
		case ($diff < 172800):
			# less than two days
			return _('about a day ago');
			break;
		case ($diff < 604800):
			# less than a week
			if (round($diff / 86400) == 7) {
				return _('about a week ago');
			}
			return sprintf(_('%d days ago'), round($diff / 86400));
			break;
		case ($diff < 691200):
			# a week an a day
			return _('about a week ago');
			break;
		case ($diff < 2764800):
			# less than a month
			if (round($diff / 691200) == 1) {
				return _('about a week ago');
			}
			return sprintf(_('%d weeks ago'), round($diff / 691200));
			break;
		case ($diff < 4579200):
			# a month and three weeks
			return _('about a month ago');
			break;
		case ($diff < 33177600);
			# less than a year
			return sprintf(_('%d months ago'), round($diff / 2764800));
			break;
		case ($diff < 35942400):
			# a year and a month
			return _('about a year ago');
			break;
		case ($diff > 35942400):
			return _('more than a year ago');
			break;
	}
}
