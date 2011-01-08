<div id="player">
	<audio id="audio" autobuffer>
		{if $track->streamurl}
			<object id="fallbackembed" style="width:200px;height:50px;" type="application/ogg" data="{$track->streamurl}"><a type="application/ogg" rel="enclosure" href="{$track->streamurl}">Listen to this track</a></object>
		{elseif isset($radio_session)}

			<p>Player problems? The player currently works in <a href='http://www.chromium.org/Home'>Chromium</a> and <a href='http://www.gnu.org/software/gnuzilla/'>Icecat</a>/<a href='http://www.mozilla.org/en/firefox/'>Firefox</a> 3.5 or later &mdash; it may also work in Chrome and Opera, though we don't recommend them.</p><p>Firefox users may experience problems under Ubuntu due to <a href='https://bugs.launchpad.net/ubuntu/+source/firefox/+bug/450684'>bug #450684</a>.</p>			

		{/if}
	</audio>
	<div id="interface">
		<div id="trackinfo">
			<span id="artistname"></span> - <span id="trackname"></span> <span id="showplaylist"><a href="#" onclick="togglePlaylist(); return false;"><img src="{$img_url}/player/show-playlist.png" alt="Show playlist" title="Show playlist" /></a></span><span id="hideplaylist"><a href="#" onclick="togglePlaylist(); return false;"><img src="{$img_url}/player/hide-playlist.png" alt="Hide playlist" title="Hide playlist" /></a></span>
			<div id="playlist">
				<br />
				<strong><u>{t}Playlist{/t}</u></strong>
				<ul id="songs">
				</ul>
			</div>
		</div>
		<div id="progress">
			<div id="progressbar"></div>
			<span id="scrobbled">Scrobbled</span>
			<span id="currenttime"></span>/<span id="duration"></span>
		</div>
		<div id="p_buttons">
			{if $logged_in}
			<a href="#" onclick="ban(); return false;" id="ban"><img src="{$img_url}/player/ban.png" alt="Ban" title="Ban" /></a>
			{/if}
			<a href="#" onclick="skipBack(); return false;" id="skipback"><img src="{$img_url}/player/skip-backward.png" alt="Skip Backwards" title="Skip Backwards" /></a>
			<a href="#" onclick="seekBack(); return false;" id="seekback"><img src="{$img_url}/player/seek-backward.png" alt="Seek Backwards" title="Seek Backwards" /></a>
			<a href="#" onclick="play(); return false;" id="play"><img src="{$img_url}/player/play.png" alt="Play" title="Play" /></a>
			<a href="#" onclick="pause(); return false;" id="pause"><img src="{$img_url}/player/pause.png" alt="Pause" title="Pause" /></a>
			<a href="#" onclick="seekForward(); return false;" id="seekforward"><img src="{$img_url}/player/seek-forward.png" alt="Seek Forwards" title="Seek Forwards" /></a>
			<a href="#" onclick="skipForward(); return false;" id="skipforward"><img src="{$img_url}/player/skip-forward.png" alt="Skip Forwards" title="Skip Forwards" /></a>
			{if $logged_in}
			<a href="#" onclick="toggleTag(); return false;" id="open_tag"><img src="{$img_url}/player/open-tag.png" alt="Tag" title="Tag" /></a>
			<a href="#" onclick="toggleTag(); return false;" id="close_tag" style='display: none;'><img src="{$img_url}/player/close-tag.png" alt="Tag" title="Tag" /></a>
			<a href="#" onclick="love(); return false;" id="love"><img src="{$img_url}/player/love.png" alt="Love" title="Love" /></a>
			{/if}
		</div>
		<div id="tag_input" style="display: none";>
			<p>Enter a list of tags separated by commas:<br />
			<input type='text' id='tags' name='tags' style='width: 75%; margin-top: 5px;' /><button id='tag_button' onclick="tag()" style='width: 20%; margin-top: 4px; float: right;'>Tag</button></p>
			{literal}
			<script type="text/javascript">
				$("#tags").keyup(function(event){
					if(event.keyCode == 13){
						tag();
					}
				});
			</script>
			{/literal}
		</div>
	</div>
<br />
	<a href='#' onclick='$("#playerproblems").toggle(1000)'>Player problems?</a>
	<div id='playerproblems' style='display: none;'>
		<p>The player currently works in <a href='http://www.chromium.org/Home'>Chromium</a> and <a href='http://www.gnu.org/software/gnuzilla/'>Icecat</a>/<a href='http://www.mozilla.org/en/firefox/'>Firefox</a> 3.5 or later &mdash; it may also work in Chrome and Opera, though we don't recommend them.</p>
	</div>
</div>
