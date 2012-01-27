{include file='header.tpl'}

<center>
	<div id='player-container' style='float: none; text-align: center;'>
		<h4>{t user=$me->name|capitalize}%1's Recommended Radio{/t}</h4><br />
		{include file='player.tpl'}
		<script type="text/javascript">
			{if isset($this_user)}
				playerInit(false, "{$this_user->getScrobbleSession()}", "{$this_user->getWebServiceSession()}", "{$radio_session}");
			{else}
				playerInit(false, false, false, "{$radio_session}");
			{/if}
		</script>
	</div>
</center>

{include file='footer.tpl'}
