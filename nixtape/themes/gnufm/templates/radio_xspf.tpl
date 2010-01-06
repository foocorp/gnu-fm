<?xml version="1.0" encoding="utf-8"?>
<lfm status="ok">
<playlist version="1" xmlns="http://xspf.org/ns/0/">
<title>{$title}</title>
<creator>GNU FM</creator>
<date>{$date}</date>
<link rel="http://www.last.fm/expiry">3600</link>
<trackList>

{section name=rt loop=$radiotracks}
	<track>
	    <location>{$radiotracks[rt].location|escape:"html":"UTF-8"}</location>
	    <title>{$radiotracks[rt].title|escape:"html":"UTF-8"}</title>
	    <identifier>{$radiotracks[rt].id|escape:"html":"UTF-8"}</identifier>
	    <album>{$radiotracks[rt].album|escape:"html":"UTF-8"}</album>
	    <creator>{$radiotracks[rt].creator|escape:"html":"UTF-8"}</creator>
	    <duration>{$radiotracks[rt].duration|escape:"html":"UTF-8"}</duration>
	    <image>{$radiotracks[rt].image|escape:"html":"UTF-8"}</image>
	    <extension application="/">
		<trackauth>00000</trackauth>
		<albumid>00000</albumid>
		<artistid>00000</artistid>
		<recording>00000</recording>
		<artistpage>{$radiotracks[rt].artisturl|escape:"html":"UTF-8"}</artistpage>
		<albumpage>{$radiotracks[rt].albumurl|escape:"html":"UTF-8"}</albumpage>
		<trackpage>{$radiotracks[rt].trackurl|escape:"html":"UTF-8"}</trackpage>
		<buyTrackURL></buyTrackURL>
		<buyAlbumURL></buyAlbumURL>
		<freeTrackURL>{$radiotracks[rt].downloadurl|escape:"html":"UTF-8"}</freeTrackURL>
	    </extension>
	</track>
{/section}

</trackList>
</playlist>
</lfm>
