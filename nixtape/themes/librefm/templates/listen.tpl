{include file='header.tpl'}

<h2>Go ahead, listen all you want</h2>

{if isset($radio_session)}
{include file='player.tpl'}
<div id='error'></div>
<script type="text/javascript">
	{if isset($this_user)}
	playerInit(false, "{$this_user->getScrobbleSession()}", "{$radio_session}");
	{else}
	playerInit(false, false, "{$radio_session}");
	{/if}
</script>
{else}
<h3>Listen to 100% libre music right here in your web browser!</h3>

<p>To get started, simply enter the type of music you'd like to hear
or select one of the common tags below:</p>

<p><a href="?tag=folk">Folk</a> <a href="?tag=rock">Rock</a> <a href="?tag=metal">Metal</a> <a href="?tag=classical">Classical</a> <a href="?tag=pop">Pop</a> <a href="?tag=blues">Blues</a> <a href="?tag=jazz">Jazz</a> <a href="?tag=punk">Punk</a> <a href="?tag=ambient">Ambient</a></p>

<form method='get' action=''>
<div><label for="tag">{t}Custom tag:{/t}</label><input type="text" id="tag" name="tag" /></div>
<div><input type="submit" value="Listen" /></div></form>
{/if}

{include file='footer.tpl'}
