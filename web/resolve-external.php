<?php
function resolve_external_url($url) {
	if (substr($url, 0, 10) == "jamendo://") {
		return process_jamendo_url($url);
	} 

	return $url;	
}

function process_jamendo_url($url) {
	if (substr($url, 10, 13) == "track/stream/") {
		$id = substr($url, 23);
		return "http://api.jamendo.com/get2/stream/track/redirect/?id=" . $id . "&streamencoding=ogg2";
	}

	if (substr($url, 10, 15) == "album/download/") {
		$id = substr($url, 25);
		return "http://api.jamendo.com/get2/bittorrent/file/plain/?album_id=" . $id . "&type=archive&class=ogg3";
	}

	if (substr($url, 10, 10) == "album/art/") {
		$id = substr($url, 20);
		return "http://api.jamendo.com/get2/image/album/redirect/?id=" . $id . "&imagesize=400";
	}

	// We don't know what this is
	return $url;
}

?>
