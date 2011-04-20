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


require_once('database.php');
require_once('templating.php');
require_once('data/Album.php');
require_once('data/Track.php');
require_once('utils/licenses.php');

try {
	$artist = new Artist(urldecode($_GET['artist']));
} catch (Exception $e) {
	$smarty->assign('pageheading', 'Artist not found.');
	$smarty->assign('details', 'The artist ' . $_GET['artist'] . ' was not found in the database.');
	$smarty->display('error.tpl');
	die();
}

$album = new Album(urldecode($_GET['album']), $artist->name);

if (!isset($this_user) || !$this_user->manages($artist->name)) {
	$smarty->assign('pageheading', 'Permission denied');
	$smarty->assign('error', 'You don\'t have permission to edit this artist\'s details.');
	$smarty->display('error.tpl');
	die();
}

$edit = false;
if (isset($_GET['track'])) {
	$edit = true;
	$track = new Track(urldecode($_GET['track']), $artist->name);
}

$smarty->assign('artist', $artist);
$smarty->assign('edit', $edit);
if ($edit) {
	$name = $track->name;
	$smarty->assign('name', $name);
	$smarty->assign('streaming_url', $track->streamurl);
	$smarty->assign('pageheading', '<a href="' . $artist->getURL() . '">' . $artist->name . '</a> &mdash; Edit Track');
} else {
	$smarty->assign('pageheading', '<a href="' . $artist->getURL() . '">' . $artist->name . '</a> &mdash; Add Track');
}

if (isset($_POST['submit'])) {

	if (!$edit) {
		if (empty($_POST['name'])) {
			$errors[] = 'A track name must be specified.';
		}
		$name = $_POST['name'];
	}

	if (empty($_POST['streaming_url'])) {
		$errors[] = 'A streaming URL must be specified.';
	}
	$streaming_url = $_POST['streaming_url'];
	if (substr($streaming_url, 0, 7) != 'http://') {
		$streaming_url = 'http://' . $streaming_url;
	}

	if (!preg_match('/http:\/\/(www.)?archive.org\/download\/([^\/]*)\/.*/', $streaming_url, $matches)) {
		$errors[] = 'The streaming URL must be hosted at archive.org. Make sure you aren\'t linking to a mirror location (URL should begin with http://www.archive.org/...).';
	} else {
		// Check we've been given correct file types
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$type = $finfo->buffer(file_get_contents($streaming_url, false, null, -1, 12));
		if ($type != 'application/ogg') {
			$errors[] = 'File must be in Ogg Vorbis format.';
		}

		// Check the license
		$archive_name = $matches[2];
		$meta_url = 'http://www.archive.org/download/' . $archive_name . '/' . $archive_name . '_meta.xml';
		try {
			$meta = simplexml_load_file($meta_url);
			$license = $meta->licenseurl;
			if (!is_free_license($license)) {
				$errors[] = 'Sorry, we don\'t recognise the license that this item is under as being free enough. We currently support CC-0, CC-BY, CC-BY-SA and Art Libre, if you think we should add this license please get in touch.';
			}
		} catch (Exception $e) {
			$errors[] = 'This doesn\'t appear to be a valid archive.org item.';
		}

	}


	if ($errors) {
		$smarty->assign('errors', $errors);
		$smarty->assign('name', $name);
		$smarty->assign('streaming_url', $streaming_url);
	} else {
		// If the creation was successful send the user back to the view page
		if ($edit) {
			$track->setStreamURL($streaming_url);
			$track->setDownloadURL($streaming_url);
			$track->setLicense($license);
		} else {
			$track = Track::create($name, $artist->name, $album->name, $streaming_url, $streaming_url, $license);
		}
		header('Location: ' . $track->getURL());
	}
}

$smarty->display('track-add.tpl');
