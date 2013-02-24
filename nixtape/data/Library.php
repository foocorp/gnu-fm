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
require_once($install_path . '/data/sanitize.php');

/**
 * Provides access to functions involving a user's library.
 */
class Library {

	/**
	 * Remove a scrobble.
	 *
	 * @param int userid User ID.
	 * @param int timestamp Timestamp in Unix time.
	 * @param string artist Artist name.
	 * @param string track Track name.
	 * @return bool True if scrobble was removed, False if not.
	 */
	function removeScrobble($userid, $timestamp, $artist, $track) {
		global $adodb;

		$delete_query = 'DELETE FROM Scrobbles WHERE userid=? AND time=? AND artist=? AND track=?';
		$delete_params = array((int)$userid, (int)$timestamp, $artist, $track);

		// TODO Should we have a db trigger for this?
		$update_stats_query = 'UPDATE User_Stats SET scrobble_count=scrobble_count-1 WHERE userid=?';
		$update_stats_params = array((int)$userid);

		$adodb->StartTrans();
		try {
			$adodb->Execute($delete_query, $delete_params);
			$delete_count = $adodb->Affected_Rows();
			if($delete_count) {
				$adodb->Execute($update_stats_query, $update_stats_params);
			}
		} catch (Exception $e) {
			$adodb->FailTrans();
			$adodb->CompleteTrans();
			reportError($e->getMessage(), $e->getTraceAsString());
			return False;
		}
		$adodb->CompleteTrans();

		return (bool)$delete_count;
	}
		
}

