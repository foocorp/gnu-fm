{include file='header.tpl'}

<h2>Listen</h2><br />

{if isset($station)}
	{if isset($u_user)}
		{include file='player.tpl'}
<div id='error'></div>
<script type="text/javascript">
	{if isset($u_user)}
	playerInit(false, "{$u_user->getScrobbleSession()}", "{$u_user->getRadioSession($station)}");
	{/if}
</script>
	{else}
<p>Sorry, you need to <a href='{$base_url}/login.php'>login</a> to be able to listen to radio streams.</p>
	{/if}
{else}
<p>To listen to 100% free (libre) music simply enter the type of music you'd like to hear or select one of the common tags below:</p>
<p><a href="?tag=folk">Folk</a> <a href="?tag=rock">Rock</a> <a href="?tag=metal">Metal</a> <a href="?tag=classical">Classical</a> <a href="?tag=pop">Pop</a> <a href="?tag=blues">Blues</a> <a href="?tag=jazz">Jazz</a> <a href="?tag=punk">Punk</a> <a href="?tag=ambient">Ambient</a></p>
<p><form method='get' action=''><label for="tag">Custom tag:</label> <input type="text" id="tag" name="tag" /> <input type="submit" value="Listen" /></form></p>
{/if}
<br />

<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
