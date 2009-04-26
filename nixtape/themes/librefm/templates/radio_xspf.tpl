<playlist version="1" xmlns:lastfm="http://www.audioscrobbler.net/dtd/xspf-lastfm">
<title>{$title}</title>
<creator>libre.fm</creator>
<link rel=\"http://www.last.fm/skipsLeft\">9999</link>
<trackList>

{section name=rt loop=$radiotracks}
	<track>
	    <location>{$rt["location"]|escape:"html":"UTF-8"}</location>
	    <title>{$rt["title"]|escape:"html":"UTF-8"}</title>\n";
	    <id>{$rt["id"]|escape:"html":"UTF-8"}</id>
	    <album>{$rt["album"]|escape:"html":"UTF-8"}</album>
	    <creator>{$rt["creator"]|escape:"html":"UTF-8"}</creator>
	    <duration>{$rt["duration"]|escape:"html":"UTF-8"}</duration>
	    <image>{$rt["image"]|escape:"html":"UTF-8"}</image>
	    <link rel="http://www.last.fm/artistpage">{$rt["artisturl"]|escape:"html":"UTF-8"}</link>
	    <link rel="http://www.last.fm/albumpage">{$rt["albumurl"]|escape:"html":"UTF-8"}</link>
	    <link rel="http://www.last.fm/trackpage">{$rt["trackurl"]|escape:"html":"UTF-8"}</link>
	    <link rel="http://www.last.fm/buyTrackURL"></link>
	    <link rel="http://www.last.fm/buyAlbumURL"></link>
	    <link rel="http://www.last.fm/freeTrackURL">{$rt["downloadurl"]|escape:"html":"UTF-8"}</link>
	</track>
{/section}

</trackList>
</playlist>
