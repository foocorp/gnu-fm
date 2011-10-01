<div class='sideblock'>
	<h3>{t}Neighbours{/t}</h3>
	<div id='neighbours'>
		{foreach from=$neighbours item=neighbour}
			<a href='{$neighbour.user->getURL()}'><img src='{$neighbour.user->getAvatar()|escape:'html'}' class='neighbour' alt='{$neighbour.user->name|escape:'html':'UTF-8'}' title='{$neighbour.user->name|escape:'html':'UTF-8'}' /></a>
		{/foreach}

		<br /><br />

		{if $isme}
			{t}These folks all have excellent taste!{/t}
		{else}
			{t name=$me->name|escape:'html':'UTF-8'}These folks all have similar tastes to %1.{/t}
		{/if}
	</div>
</div>
