{include file='header.tpl'}

	<div class="yui-gc">
    <div class="yui-u first" id="new-libre-fm" style="background-color: black; color: white;">
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
    <div class="yui-u" id="sidebar">

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

{include file='footer.tpl'}
