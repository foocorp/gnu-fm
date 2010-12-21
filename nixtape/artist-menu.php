<?
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

function artist_menu($artist, $active_page) {
	global $this_user;

	$submenu = array(
		array('name' => 'Overview', 'url' => $artist->getURL()),
		array('name' => 'Tag', 'url' => $artist->getURL('tag')),
	);

	foreach ($submenu as &$item) {
		$item['active'] = ($item['name'] == $active_page);
	}

	return $submenu;
}
?>
