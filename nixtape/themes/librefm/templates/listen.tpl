{include file='header.tpl'}

<h2>Listen</h2><br />

{if isset($station)}
	{if isset($this_user)}
		{include file='player.tpl'}
<div id='error'></div>
<script type="text/javascript">
	{if isset($this_user)}
	playerInit(false, "{$this_user->getScrobbleSession()}", "{$this_user->getRadioSession($station)}");
	{/if}
</script>
	{else}
<p>{t}Sorry, you need to <a href='{$base_url}/login.php'>login</a> to be able to listen to radio streams.{/t}</p>
	{/if}
{else}
<p>{t}To listen to 100% free (libre) music simply enter the type of music you'd like to hear or select one of the common tags below:{/t}</p>
<p><a href="?tag=folk">Folk</a> <a href="?tag=rock">Rock</a> <a href="?tag=metal">Metal</a> <a href="?tag=classical">Classical</a> <a href="?tag=pop">Pop</a> <a href="?tag=blues">Blues</a> <a href="?tag=jazz">Jazz</a> <a href="?tag=punk">Punk</a> <a href="?tag=ambient">Ambient</a></p>
<p><form method='get' action=''><label for="tag">{t}Custom tag:{/t}</label> <input type="text" id="tag" name="tag" /> <input type="submit" value="Listen" /></form></p>
{/if}
<br />

<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
