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
require_once($install_path . '/data/User.php');

class UserJSON {

	public static function getInfo($username) {

		$user = new User($username);
		if (!$user) {
			$json_data = array('error' => 6, 'message' => 'No user with that name was found');
			return json_encode($json_data);
		}

		$json_data = array('user' => array(	'name' => $user->name,
							'homepage' => $user->homepage,
							'location' => $user->location,
							'bio' => $user->bio,
							'profile_create' => strftime('%c', $user->created)));

		if (isset($user->modified))
			$json_data['user']['profile_updated'] = strftime('%c', $user->modified);

		return json_encode($json_data);
	}


}
