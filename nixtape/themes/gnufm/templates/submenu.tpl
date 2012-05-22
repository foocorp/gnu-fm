{if isset($submenu)}
<div id='submenu'>
	<ul>
	{section name=i loop=$submenu}
		<li {if $submenu[i].active}class='active'{/if}>
			<a href='{$submenu[i].url}'>{$submenu[i].name}</a>
		</li>
	{/section}
	</ul>
</div>
{/if}
