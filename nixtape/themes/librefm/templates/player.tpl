<div id="player">
	<audio id="audio">
		{if $track->streamurl}
			<object id="fallbackembed" style="width:200px;height:50px;" type="application/ogg" data="{$track->streamurl}"><a type="application/ogg" rel="enclosure" href="{$track->streamurl}">Listen to this track</a></object>
		{elseif isset($radio_session)}
			
			<p>Player problems? The player currently works
			in Icecat/Firefox 3.5 or later &mdash; it may
			also work in Chrome, Safari and Opera, though
			we don't recommend them.</p>

		{/if}
	</audio>
	<div id="interface">
		<div id="trackinfo">
			<span id="artistname"></span> - <span id="trackname"></span> <span id="showplaylist"><a href="#" onclick="togglePlaylist()"><img src="{$media_url}/{$default_theme}/img/player/show-playlist.png" alt="Show playlist" title="Show playlist" /></a></span><span id="hideplaylist"><a href="#" onclick="togglePlaylist()"><img src="{$media_url}/{$default_theme}/librefm/img/player/hide-playlist.png" alt="Hide playlist" title="Hide playlist" /></a></span>
			<div id="playlist">
				<hr />
				<strong><u>{t}Playlist{/t}</u></strong>
				<ul id="songs">
				</ul>
			</div>
		</div>
		<div id="progress">
			<div id="progressbar"></div>
			<span id="currenttime"></span>/<span id="duration"></span>
		</div>
		<span id="scrobbled">Scrobbled</span>
		<div id="buttons">
			<a href="#" onclick="skipBack()" id="skipback"><img src="{$media_url}/{$default_theme}/img/player/skip-backward.png" alt= "Skip Backwards" /></a>
			<a href="#" onclick="seekBack()" id="seekback"><img src="{$media_url}/{$default_theme}/img/player/seek-backward.png" alt="Seek Backwards" /></a>
			<a href="#" onclick="play()" id="play"><img src="{$media_url}/{$default_theme}/img/player/play.png" alt="Play" /></a>
			<a href="#" onclick="pause()" id="pause"><img src="{$media_url}/{$default_theme}/img/player/pause.png" alt="Pause" /></a>
			<a href="#" onclick="seekForward()" id="seekforward"><img src="{$media_url}/{$default_theme}/img/player/seek-forward.png" alt="Seek Forwards" /></a>
			<a href="#" onclick="skipForward()" id="skipforward"><img src="{$media_url}/{$default_theme}/img/player/skip-forward.png" alt="Skip Forwards" /></a>
		</div>
	</div>
</div>

		{if !$track->streamurl}
		
		<p>Sorry, this track doesn't offer you the ability to
		<a href="http://freedomdefined.org/">legally share
		this song</a>, so we're unable to bring you a
		stream/download.</p>

		<p>If you feel this is a mistake, please <a href="http://bugs.libre.fm/newticket?summary={$track->name} by {$track->artist_name} is a free track&component=website-alpha">let us know</a>.</p>

		{else}

		<p><a href="http://freedomdefined.org/"><img
		src="http://freedomdefined.org/upload/b/bf/Mfalzon-freecontent_logo01--wikilogo.png"
		alt="Free music" /></a></p>

		{/if}