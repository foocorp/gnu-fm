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

		{include file='player.tpl'}

		<script type="text/javascript">
		{if isset($this_user)}
			playerInit(false, "{$this_user->getScrobbleSession()}", "{$radio_session}");
		{else}
			playerInit(false, false, "{$radio_session}");
		{/if}
		</script>

	</div>

    <div id="downloads">

{if !isset($this_user) || $this_user->anticommercial<>1}
    {include file='adbard.tpl'}
{/if}

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
