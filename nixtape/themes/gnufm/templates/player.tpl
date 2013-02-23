<div id="player">

	<audio id="audio" autobuffer>
		{if $track->streamurl}
			<object id="fallbackembed" type="application/ogg" data="{$track->streamurl}"><a type="application/ogg" rel="enclosure" href="{$track->streamurl}">Listen to this track</a></object>
		{/if}
	</audio>

	<p id="loading"></p>
	<div id="interface">

		<div id="trackinfo">
			<span id="artistname"></span> - <span id="trackname"></span><img id="showplaylist" class="btn" src="{$img_url}/player/show-playlist.png" alt="{t}Show playlist{/t}" title="{t}Show playlist{/t}" /><img id="hideplaylist" class="btn" src="{$img_url}/player/hide-playlist.png" alt="{t}Hide playlist{/t}" title="{t}Hide playlist{/t}" />
			<div id="tracktags">
				<ul>
				</ul>
			</div>
		</div>

		<div id="progress">
			<div id="progress-slider" title="Seek to time"></div>
			<span id="scrobbled">Scrobbled</span>
			<span id="currenttime"></span>/<span id="duration"></span>
		</div>

		<div id="p_buttons">
			<img id="ban" class="btn" src="{$img_url}/player/ban.png" alt="{t}Ban{/t}" title="{t}Ban{/t}" />
			<img id="skipback" class="btn" src="{$img_url}/player/skip-backward.png" alt="{t}Skip Backwards{/t}" title="{t}Skip Backwards{/t}" />
			<img id="seekback" class="btn" src="{$img_url}/player/seek-backward.png" alt="{t}Seek Backwards{/t}" title="{t}Seek Backwards{/t}" />
			<img id="play" class="btn" src="{$img_url}/player/play.png" alt="{t}Play{/t}" title="{t}Play{/t}" />
			<img id="pause" class="btn" src="{$img_url}/player/pause.png" alt="{t}Pause{/t}" title="{t}Pause{/t}" />
			<img id="seekforward" class="btn" src="{$img_url}/player/seek-forward.png" alt="{t}Seek Forwards{/t}" title="{t}Seek Forwards{/t}" />
			<img id="skipforward" class="btn" src="{$img_url}/player/skip-forward.png" alt="{t}Skip Forwards{/t}" title="{t}Skip Forwards{/t}" />
			<img id="open_tag" class="btn" src="{$img_url}/player/open-tag.png" alt="{t}Tag{/t}" title="{t}Tag{/t}" />
			<img id="close_tag" class="btn" src="{$img_url}/player/close-tag.png" alt="{t}Tag{/t}" title="{t}Tag{/t}" />
			<img id="love" class="btn" src="{$img_url}/player/love.png" alt="{t}Love{/t}" title="{t}Love{/t}" />
			<img id="volume" class="btn" src="{$img_url}/player/volume-medium.png" alt="{t}Volume{/t}" title="{t}Volume{/t}" />
		</div>

		<div id="volume-box">
			<div id="volume-slider"></div>
		</div>

		<div id="tag_input">
			<p>{t}Enter a list of tags separated by commas:{/t}<br />
			<input type='text' id='tags' name='tags' placeholder="guitar, violin, female vocals, piano"/><button id='tag_button'>{t}Tag{/t}</button></p>
		</div>

		<div id="playlist">
			<ul id="songs">
			</ul>
		</div>

	</div>

	<br />

	<span id="toggleproblems">{t}Player problems?{/t}</span>
	<div id="problems">
		<p>{t escape=no}The player currently works in <a href='http://www.chromium.org/Home'>Chromium</a> and <a href='http://www.gnu.org/software/gnuzilla/'>Icecat</a>/<a href='http://www.mozilla.org/en/firefox/'>Firefox</a> 3.5 or later &mdash; it may also work in Chrome and Opera, though we don't recommend them.{/t}</p>
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
