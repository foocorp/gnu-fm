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

@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);

header('Content-type: text/html; charset=utf-8');
require_once('database.php');
require_once('utils/human-time.php');

$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
$res = $adodb->GetAll('SELECT artist, track from Scrobbles where album is null LIMIT 20;');

echo '<ul>';

if (!$res) {
	die('sql error');
}
foreach ($res as &$row) {

	echo '<li>' . $row['artist'] . '&mdash;' . $row['track'] . '</li>';

	echo 'Finding album...';

	echo doABunchOfShit($row['artist'], $row['track']);

	for ($i = 0; $i < ob_get_level(); $i++) {
		ob_end_flush();
	}
	ob_implicit_flush(1);

}

function doABunchOfShit($artist, $track) {

	 $album = ScrobbleLookup($artist, $track);

	 if ($album) {
		 return $album;
	 } else {
		 $album = BrainzLookup($artist, $track);
		 return $album;
	 }

}

function ScrobbleLookup($artist, $track) {
	global $adodb;

	$sql = 'SELECT album from Scrobbles where artist = ' . $adodb->qstr($artist) . ' and track = ' . $adodb->qstr($track) . ' LIMIT 1;';

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	$album = $adodb->GetOne($sql);

	if (!$album) {
		die('sql error');
	}

	return $album;
}

function BrainzLookup($artist, $track) {

	global $adodb;

	$sql = 'select a.name as artist,l.name as album, t.name as track,t.gid as mbid from brainz.track t left join brainz.artist a on t.artist=a.id left join brainz.albumjoin j on j.track=t.id left join brainz.album l on l.id=j.album where lower(t.name) = lower(' . $adodb->qstr($track) . ') and lower(a.name) = lower(' . $adodb->qstr($artist) . ') LIMIT 1;';

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	$albumData = $adodb->GetRow($sql);

	if (!$albumData)) {
		die('sql error');
	}

	return $albumData['album'];
}
?>
		</ul>
