{include file='header.tpl'}

<center>
	<h4>
		{if $type != 'loved'}<a href='{$me->getURL('station')}'>{/if}{t name=$me->name|capitalize}%1's Loved Radio{/t}{if $type != 'loved'}</a>{/if} | 
		{if $type != 'recommended'}<a href='{$me->getURL('station')}?type=recommended'>{/if}{t name=$me->name|capitalize}%1's Recommended Radio{/t}{if $type != 'recommended'}</a>{/if} | 
		{if $type != 'mix'}<a href='{$me->getURL('station')}?type=mix'>{/if}{t name=$me->name|capitalize}%1's Mix Radio{/t}{if $type != 'mix'}</a>{/if} |
		{if $type != 'neighbours'}<a href='{$me->getURL('station')}?type=neighbours'>{/if}{t name=$me->name|capitalize}%1's Neighbourhood Radio{/t}{if $type != 'neighbours'}</a>{/if}
	</h4>

	<div id='player-container' style='float: none; text-align: center;'>

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
