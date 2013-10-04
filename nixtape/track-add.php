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
require_once('utils/oggclass/ogg.class.php');

try {
	$artist = new Artist($_GET['artist']);
} catch (Exception $e) {
	displayError("Artist not found",
		"The artist {$_GET['artist']} was not found in the database.");
}

try {
	$album = new Album($_GET['album'], $artist->name);
} catch (Exception $e) {
	displayError("Album not found",
		"The album {$_GET['album']} by artist {$artist->name} was not found in the database.");
}

if (!isset($this_user) || !$this_user->manages($artist->name)) {
	displayError("Permission denied",
		"You don't have permission to edit this artist's details");
}

$edit = false;
if (isset($_GET['track'])) {
	$edit = true;

	try {
		$track = new Track($_GET['track'], $artist->name);
	} catch (Exception $e) {
		displayError("Track not found",
			"The track {$_GET['track']} by artist {$artist->name} was not found in the database.");
	}
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

	if (preg_match('@http://[^/]*archive.org/.*/items/([^/]*)/([^/]*)@', $streaming_url, $matches)) {
		// Convert mirror URL into canonical URL
		$streaming_url = 'http://www.archive.org/download/' . $matches[1] . '/' . $matches[2];
	}

	if (!preg_match('@http://(www.)?archive.org/download/([^\/]*)/.*@', $streaming_url, $matches)) {
		$errors[] = 'Sorry, the streaming URL must be hosted at archive.org.';
	} else {
		// Check we've been given correct file types
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$type = $finfo->buffer(file_get_contents($streaming_url, false, null, -1, 12));
		if ($type == 'application/ogg') {
			// Check metadata matches artist/album/track names
			$ogg = new Ogg($streaming_url, NOCACHING);
			$tag_errors = false;
			foreach($ogg->Streams['vorbis']['comments'] as $comment) {
				if (preg_match('/^ARTIST=(.*)$/i', $comment, $comment_matches)) {
					if (strtolower($comment_matches[1]) != strtolower($artist->name)) {
						$errors[] = 'The artist tag in the uploaded file doesn\'t match your current artist name. The artist tag should be: "' . $artist->name . '", but currently it\'s: "' . $comment_matches[1] . '".';
						$tag_errors = true;
					}
				} elseif (preg_match('/^ALBUM=(.*)$/i', $comment, $comment_matches)) {
					if (strtolower($comment_matches[1]) != strtolower($album->name)) {
						$errors[] = 'The album tag in the uploaded file doesn\'t match the name of the album you\'re editing. The album tag should be: "' . $album->name . '", but currently it\'s: "' . $comment_matches[1] . '".';
						$tag_errors = true;
					}
				} elseif (preg_match('/^TITLE=(.*)$/i', $comment, $comment_matches)) {
					if (strtolower($comment_matches[1]) != strtolower($name)) {
						$errors[] = 'The track name tag in the uploaded file doesn\'t match the name of the track you\'re editing. The track name tag should be: "' . $name . '", but currently it\'s: "' . $comment_matches[1] . '".';
						$tag_errors = true;
					}
				}
			}

			if($tag_errors) {
				$errors[] = 'Errors in the artist, album or track name tags stop us from being able to track statistics about your song correctly. Please correct these, reupload your file to archive.org and try again. You can use software such as <a href="http://easytag.sourceforge.net/">EasyTag</a> to help you tag your files correctly.';
			}

			// Check we aren't being linked to a file with an unstreamably high bitrate
			if($ogg->Streams['vorbis']['bitrate'] > 192000) {
				$errors[] = 'Maximum bitrate should be no higher than 192kbps.';
			}
		} else {
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
