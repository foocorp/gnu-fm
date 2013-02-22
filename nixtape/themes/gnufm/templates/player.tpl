<div id="player">

	<audio id="audio" autobuffer>
		{if $track->streamurl}
			<object id="fallbackembed" style="width:200px;height:50px;" type="application/ogg" data="{$track->streamurl}"><a type="application/ogg" rel="enclosure" href="{$track->streamurl}">Listen to this track</a></object>
		{elseif isset($radio_session)}

			<p>{t escape=no}Player problems? The player currently works in <a href='http://www.chromium.org/Home'>Chromium</a> and <a href='http://www.gnu.org/software/gnuzilla/'>Icecat</a>/<a href='http://www.mozilla.org/en/firefox/'>Firefox</a> 3.5 or later &mdash; it may also work in Chrome and Opera, though we don't recommend them.</p><p>Firefox users may experience problems under Ubuntu due to <a href='https://bugs.launchpad.net/ubuntu/+source/firefox/+bug/450684'>bug #450684</a>.{/t}</p>

		{/if}
	</audio>

	<div id="interface">

		<div id="trackinfo">
			<span id="artistname"></span> - <span id="trackname"></span> <span id="showplaylist"><a href="#" onclick="togglePlaylist(); return false;"><img src="{$img_url}/player/show-playlist.png" alt="{t}Show playlist{/t}" title="{t}Show playlist{/t}" /></a></span><span id="hideplaylist"><a href="#" onclick="togglePlaylist(); return false;"><img src="{$img_url}/player/hide-playlist.png" alt="{t}Hide playlist{/t}" title="{t}Hide playlist{/t}" /></a></span>
			{if $logged_in}
			<div id="tracktags">
				<ul>
				</ul>
			</div>
			{/if}
		</div>

		<div id="progress">
			<div id="progress-slider" title="Seek to time"></div>
			<span id="scrobbled">Scrobbled</span>
			<span id="currenttime"></span>/<span id="duration"></span>
		</div>

		<div id="p_buttons">
			{if $logged_in}
			<a href="#" onclick="ban(); return false;" id="ban"><img src="{$img_url}/player/ban.png" alt="{t}Ban{/t}" title="{t}Ban{/t}" /></a>
			{/if}
			<a href="#" onclick="skipBack(); return false;" id="skipback"><img src="{$img_url}/player/skip-backward.png" alt="{t}Skip Backwards{/t}" title="{t}Skip Backwards{/t}" /></a>
			<a href="#" onclick="seekBack(); return false;" id="seekback"><img src="{$img_url}/player/seek-backward.png" alt="{t}Seek Backwards{/t}" title="{t}Seek Backwards{/t}" /></a>
			<a href="#" onclick="play(); return false;" id="play"><img src="{$img_url}/player/play.png" alt="{t}Play{/t}" title="{t}Play{/t}" /></a>
			<a href="#" onclick="pause(); return false;" id="pause"><img src="{$img_url}/player/pause.png" alt="{t}Pause{/t}" title="{t}Pause{/t}" /></a>
			<a href="#" onclick="seekForward(); return false;" id="seekforward"><img src="{$img_url}/player/seek-forward.png" alt="{t}Seek Forwards{/t}" title="{t}Seek Forwards{/t}" /></a>
			<a href="#" onclick="skipForward(); return false;" id="skipforward"><img src="{$img_url}/player/skip-forward.png" alt="{t}Skip Forwards{/t}" title="{t}Skip Forwards{/t}" /></a>
			{if $logged_in}
			<a href="#" onclick="toggleTag(); return false;" id="open_tag"><img src="{$img_url}/player/open-tag.png" alt="{t}Tag{/t}" title="{t}Tag{/t}" /></a>
			<a href="#" onclick="toggleTag(); return false;" id="close_tag" style='display: none;'><img src="{$img_url}/player/close-tag.png" alt="{t}Tag{/t}" title="{t}Tag{/t}" /></a>
			<a href="#" onclick="love(); return false;" id="love"><img src="{$img_url}/player/love.png" alt="{t}Love{/t}" title="{t}Love{/t}" /></a>
			{/if}
			<a href="#" onclick="toggleVolume(); return false;" id="volume"><img src="{$img_url}/player/volume-medium.png" alt="{t}Volume{/t}" title="{t}Volume{/t}" /></a>
		</div>

		<div id="volume-box" style="display: none;">
			<div id="volume-slider"></div>
		</div>

		<div id="tag_input" style="display: none";>
			<p>{t}Enter a list of tags separated by commas:{/t}<br />
			<input type='text' id='tags' name='tags' style='width: 75%; margin-top: 5px;' /><button id='tag_button' onclick="tag()" style='width: 20%; margin-top: 4px; float: right;'>{t}Tag{/t}</button></p>
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

		<div id="playlist">
			<ul id="songs">
			</ul>
		</div>

	</div>

	<br />

	<a href='#' onclick='$("#playerproblems").toggle(1000)'>{t}Player problems?{/t}</a>
	<div id='playerproblems' style='display: none;'>
		<p>{t escape=no}The player currently works in <a href='http://www.chromium.org/Home'>Chromium</a> and <a href='http://www.gnu.org/software/gnuzilla/'>Icecat</a>/<a href='http://www.mozilla.com/en-US/firefox/'>Firefox</a> 3.5 or later &mdash; it may also work in Chrome and Opera, though we don't recommend them.{/t}</p>
	</div>

</div>

<script type="text/javascript">
	$(document).ready(function() {ldelim}
		{if $playlist == 'track'}
			var playlist = [{ldelim}"artist" : "{$track->artist_name|escape:'javascript'}", "album" : "{$track->album_name|escape:'javascript'}", "track" : "{$track->name|escape:'javascript'}", "url" : "{$track->streamurl}"{rdelim}];
			var radio_session = false;
			var station = false;
		{else}
			var playlist = false;
			var radio_session = "{$radio_session}";
			var station = "{$station}";
		{/if}
		{if isset($this_user)}
		playerInit(playlist, "{$this_user->getScrobbleSession()}", "{$this_user->getWebServiceSession()}", false, station);
		{else}
		playerInit(playlist, false, false, radio_session, false);
		{/if}
	{rdelim});
</script>
