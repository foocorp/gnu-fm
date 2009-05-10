{include file='header.tpl'}

	<div class="yui-gc" style="width: 100%;">
    <div class="yui-u first" id="new-libre-fm" style="background-color: black; color: white;">
    <div class="inner">
    <h2>{t}New on Libre.fm...{/t}</h2>

    	   <ul>
	   <li>Artist</li>
	   <li>Artist</li>
	   <li>Artist</li>
	   <li>Artist</li>
	   </ul>

     <h2>{t}Upcoming events...{/t}</h2>

    	   <ul>
	   <li>Artist</li>
	   <li>Artist</li>
	   <li>Artist</li>
	   <li>Artist</li>
	   </ul>

   </div>
</div>
    <div class="yui-u">

    <div id="radio">

    <div id="player">
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


    </div>

    <div id="downloads">

    {include file='adbard.tpl'}

    <h2>{t}Libre music downloads...{/t}</h2>

    <ul>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    <li><a href="#">Foo &mdash; Bar</a></li>
    </ul>

    <p><a href="#">{t}More Libre music downloads...{/t}</a></p>

    <p><small><a href="http://creativecommons.org/licenses/by-sa/3.0/">{t}License{/t}</a></small></p>

    </div>

</div>
</div>
    <div class="yui-gb" style="width: 100%;">
        <div class="yui-u first" id="featured-artist">

	<a href="/artist/Brad+Sucks"><img src="/themes/librefm/images/fa-bs.png" alt="Brad Sucks" /></a>

</div>
        <div class="yui-u" id="featured-group">
{t}Featured group{/t}
</div>
        <div class="yui-u" id="featured-user">
	<a href="/user/mattl"><img src="/themes/librefm/images/fu-mattl.png" alt="Matt Lee (mattl) from Boston, MA" /></a>
</div>
    </div>

{include file='footer.tpl'}
