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
require_once($install_path . '/data/Server.php');
require_once($install_path . '/data/TagCloud.php');
require_once($install_path . '/data/User.php');

/**
 * Represents Group data
 *
 * General attributes are accessible as public variables.
 *
 */
class Group {

	public $name, $owner, $fullname, $bio, $homepage, $count, $grouptype, $id, $avatar_uri, $users;

	/**
	 * User constructor
	 *
	 * @param string $name The name of the user to load
	 */
	function __construct($name, $data=null) {

		global $base_url;
		$base = preg_replace('#/$#', '', $base_url);

		if (is_array($data)) {
			$row = $data;
		}
		else {
			global $mdb2;
			$res = $mdb2->query('SELECT * FROM Groups WHERE lower(groupname) = ' . $mdb2->quote(strtolower($name), 'text'));
			
			if(PEAR::isError($res)) {
				header("Content-Type: text/plain");
				print_r($res);
				exit;
			}

			if($res->numRows()) {
				$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			}
		}
			
		if (is_array($row)) {
			$this->name         = $row['groupname'];
			$this->fullname     = $row['fullname'];
			$this->homepage     = $row['homepage'];
			$this->bio          = $row['bio'];
			$this->avatar_uri   = $row["avatar_uri"];
			$this->owner        = new User($row['owner']);
			$this->count        = -1;
			$this->users        = array();

			if (! preg_match('/\:/', $this->id))
				$this->id = $base.'/group/' . urlencode($this->name) . '#group';
		}		
	}
	
	function save ()
	{
		global $mdb2;
		
		$q = sprintf("UPDATE Groups SET "
				. "owner=%s, "
				. "fullname=%s, "
				. "homepage=%s, "
				. "bio=%s, "
				. "avatar_uri=%s, "
				. "modified=%d "
				. "WHERE groupname=%s"
				, $mdb2->quote($this->owner->name, 'text')
				, $mdb2->quote($this->fullname, 'text')
				, $mdb2->quote($this->homepage, 'text')
				, $mdb2->quote($this->bio, 'text')
				, $mdb2->quote($this->avatar_uri, 'text')
				, time()
				, $mdb2->quote($this->name, 'text'));
				
		$res = $mdb2->query($q);
		
		if(PEAR::isError($res)) {
			header("Content-Type: text/plain");
			print_r($res);
			exit;
		}

		return 1;
	}

	/**
	 * Retrieve a user's avatar via the gravatar service
	 *
	 * @param int $size The desired size of the avatar (between 1 and 512 pixels)
	 * @return A URL to the user's avatar image
	 */
	function getAvatar($size=64) {
		global $base_uri;
		if (!empty($this->avatar_uri))
			return $this->avatar_uri;
		return $base_uri . "/i/qm50.png";
	}

	function getURL() {
		return Server::getGroupURL($this->name);
	}
	
	function getUsers () {
		global $mdb2;

		if (!isset($this->users[0]))
		{
			$res = $mdb2->query("SELECT u.* "
				. "FROM Users u "
				. "INNER JOIN Group_Members gm ON u.username=gm.member "
				. "WHERE gm.groupname=".$mdb2->quote($this->name,'text'));
			if ($res->numRows())
			{
				while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					$this->users[] = new User($row['username'], $row);
				}
			}
			
			$this->count = count($this->users);
		}

		return $this->users;
	}


	function tagCloudData () {
		return TagCloud::generateTagCloud(
			'Scrobbles INNER JOIN Group_Members ON Scrobbles.username=Group_Members.member',
			'artist',
			40,
			$this->name,
			'groupname');
	}

}

