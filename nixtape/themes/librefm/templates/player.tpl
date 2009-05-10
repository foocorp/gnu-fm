<div id="player">
	<audio id="audio">
		{if $track->streamurl}
			<object id="fallbackembed" style="width:200px;height:50px;" type="application/ogg" data="{$track->streamurl}"><a type="application/ogg" rel="enclosure" href="{$track->streamurl}">Listen to this track</a></object>
		{elseif isset($radio_session)}
			<p>{t escape=no}Sorry, you need a browser capable of making use of the HTML 5 &lt;audio&gt; tag to enjoy the streaming service via the JavaScript player.{/t}</p>
		{/if}
	</audio>
	<div id="interface">
		<div id="trackinfo">
			<span id="artistname"></span> - <span id="trackname"></span> <span id="showplaylist"><a href="#" onclick="togglePlaylist()"><img src="{$base_url}/themes/librefm/images/player/show-playlist.png" alt="Show playlist" title="Show playlist" /></a></span><span id="hideplaylist"><a href="#" onclick="togglePlaylist()"><img src="{$base_url}/themes/librefm/images/player/hide-playlist.png" alt="Hide playlist" title="Hide playlist" /></a></span>
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
			<a href="#" onclick="skipBack()" id="skipback"><img src="{$base_url}/themes/librefm/images/player/skip-backward.png" alt= "Skip Backwards" /></a>
			<a href="#" onclick="seekBack()" id="seekback"><img src="{$base_url}/themes/librefm/images/player/seek-backward.png" alt="Seek Backwards" /></a>
			<a href="#" onclick="play()" id="play"><img src="{$base_url}/themes/librefm/images/player/play.png" alt="Play" /></a>
			<a href="#" onclick="pause()" id="pause"><img src="{$base_url}/themes/librefm/images/player/pause.png" alt="Pause" /></a>
			<a href="#" onclick="seekForward()" id="seekforward"><img src="{$base_url}/themes/librefm/images/player/seek-forward.png" alt="Seek Forwards" /></a>
			<a href="#" onclick="skipForward()" id="skipforward"><img src="{$base_url}/themes/librefm/images/player/skip-forward.png" alt="Skip Forwards" /></a>
		</div>
	</div>
</div>
