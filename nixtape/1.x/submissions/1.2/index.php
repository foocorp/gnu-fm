<?php
/* GNUkebox -- a free software server for recording your listening habits

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
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($install_path . 'database.php');
require_once($install_path . '1.x/scrobble-utils.php');

if (!isset($_POST['s']) || !isset($_POST['a']) || !isset($_POST['t']) || !isset($_POST['i'])) {
	die("FAILED Required POST parameters are not set\n");
}
if (empty($_POST['s']) || empty($_POST['a']) || empty($_POST['t']) || empty($_POST['i'])) {
	die("FAILED Required POST parameters are empty\n");
}

if (!is_array($_POST['a']) || !is_array($_POST['t']) || !is_array($_POST['i'])) {
	die("FAILED Track parameters must be arrays\n");
}

$session_id = $_POST['s'];

$userid = useridFromSID($session_id);
$rowvalues = array();
$forwardvalues = array();
$actualcount = 0;
$timeisstupid = 0;

for ($i = 0; $i < count($_POST['a']); $i++) {

	if (!isset($_POST['t'][$i]) || !isset($_POST['a'][$i]) || !isset($_POST['i'][$i])) {
		$f = isset($_POST['t'][$i]) ? "T({$_POST['t'][$i]})" : 't';
		$f .= isset($_POST['a'][$i]) ? "A({$_POST['a'][$i]})" : 'a';
		$f .= isset($_POST['i'][$i]) ? "I({$_POST['i'][$i]})" : 'i';

		//Add error message to db and skip to next scrobble
		reportError("FAILED Track $i was submitted with empty mandatory field(s)",
			"artist:{$_POST['a'][$i]}, album:{$_POST['b'][$i]}, track:{$_POST['t'][$i]}, time:{$_POST['i'][$i]}");
		continue;
	}

	$artist = trim($_POST['a'][$i]);
	$artist = noSpamTracks($artist);
	if (empty($artist)) {
		//Add error message to db and skip to next scrobble
		reportError("FAILED Track $i was submitted with empty artist field",
			"artist:{$_POST['a'][$i]}, album:{$_POST['b'][$i]}, track:{$_POST['t'][$i]}, time:{$_POST['i'][$i]}");
		continue;
	} else {
		switch (mb_detect_encoding($artist)) {
			case 'ASCII':
			case 'UTF-8':
				$artist = $adodb->qstr(trim(mb_strcut($artist, 0, 255, 'UTF-8')));
				break;
			default:
				die("FAILED Bad encoding in artist submission $i\n");
		}
	}

	$track = trim($_POST['t'][$i]);
	$track = noSpamTracks($track);
	if (empty($track)) {
		//Add error message to db and skip to next scrobble
		reportError("FAILED Track $i was submitted with empty track field",
			"artist:{$_POST['a'][$i]}, album:{$_POST['b'][$i]}, track:{$_POST['t'][$i]}, time:{$_POST['i'][$i]}");
		continue;
	} else {
		switch (mb_detect_encoding($track)) {
		case 'ASCII':
		case 'UTF-8':
			$track = $adodb->qstr(trim(mb_strcut($track, 0, 255, 'UTF-8')));
			break;
		default:
			die("FAILED Bad encoding in title submission $i\n");
		}
	}

	if (is_numeric($_POST['i'][$i])) {
		$time = (int) $_POST['i'][$i];
	} else {
		// 1.1 time format
		date_default_timezone_set('UTC');
		$time = strtotime($_POST['i'][$i]);
	}

	$album = trim($_POST['b'][$i]);
	$album = noSpamTracks($album);
	if (!empty($album)) {
		switch (mb_detect_encoding($album)) {
			case 'ASCII':
			case 'UTF-8':
				$album = $adodb->qstr(trim(mb_strcut($album, 0, 255, 'UTF-8')));
				break;
			default:
				die("FAILED Bad encoding in album submission $i\n");
		}
	} else {
		$album = 'NULL';
	}

	$mb = validateMBID($_POST['m'][$i]);

	if ($mb) {
		$mbid = $adodb->qstr($mb);
	} else {
		$mbid = 'NULL';
	}

	if (isset($_POST['o'][$i])) {
		$source = $adodb->qstr($_POST['o'][$i]);
	} else {
		$source = 'NULL';
	}

	if (!empty($_POST['r'][$i])) {
		$rating = $adodb->qstr($_POST['r'][$i]);
	} else {
		$rating = $adodb->qstr('0'); // use the fake rating code 0 for now
	}

	if (isset($_POST['l'][$i])) {
		$length = (int)($_POST['l'][$i]);
	} else {
		$length = 'NULL';
	}

	if (($time - time()) > 300) {
		die("FAILED Submitted track has timestamp in the future\n"); // let's try a 5-minute tolerance
	}

	if ($time <= 1009000000) {
		$timeisstupid = 1;
	}

	$failed = false;
	try {
		$exists = scrobbleExists($userid, $artist, $track, $time);
		
		if (!$exists) {
			$stid = getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid);
		}

	} catch (Exception $ex) {
		$failed = true;
		reportError($ex->getMessage(), '');
	}

	if (!$exists && $rating != 'S' && !$failed) {
		$rowvalues[$actualcount] = '('
			. $userid . ', '
			. $artist . ', '
			. $album . ', '
			. $track . ', '
			. $time . ', '
			. $mbid . ', '
			. $source . ','
			. $rating . ','
			. $length . ','
			. $stid . ')';

		// Create array with tracks to be forwarded
		if (isset($lastfm_key)) {
			$forwardvalues[$actualcount] = array(
				$userid,
				$_POST['a'][$i],
				$_POST['b'][$i],
				$_POST['t'][$i],
				$time,
				$_POST['m'][$i],
				$_POST['o'][$i],
				$_POST['r'][$i],
				$_POST['l'][$i]
			);
		}

		$actualcount++;
	}

	if (($i + 1) == count($_POST['a'])) {
		if ($actualcount > 0) {

			$adodb->StartTrans();

			for ($j = 0; $j < $actualcount; $j++) {

				// Scrobble!
				$sql = 'INSERT INTO Scrobbles (userid, artist, album, track, time, mbid, source, rating, length, stid) VALUES ' . $rowvalues[$j];
				try {
					$res =& $adodb->Execute($sql);
					if (isset($lastfm_key)) {
						call_user_func_array("forwardScrobble", $forwardvalues[$j]);
					}
				} catch (Exception $e) {
					$msg = $e->getMessage();
					$adodb->FailTrans();
					$adodb->CompleteTrans();
					reportError($msg, $sql);
				}

			}

			try {
				$adodb->CompleteTrans();
			} catch (Exception $e) {
				die('FAILED ' . $e->getMessage() . "\n");
			}

		} else {
			if ($timeisstupid == 1) {
				die("FAILED Too many submitted tracks with invalid timestamps\n");
			}
		}
	}

}

die("OK\n");
