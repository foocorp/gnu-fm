{include file='header.tpl'}
	
	<ul>
		{section name=i loop=$results}
		{if $search_type == 'artist'}
			<li><p><b><a href='{$results[i].url}'>{$results[i].name|escape:'html':'UTF-8'}</a></b><br /><small>{$results[i].bio_summary|escape:'html':'UTF-8'}</small></p></li>
		{elseif $search_type == 'user'}
			<li><p><b><a href='{$results[i].url}'>{$results[i].username|escape:'html':'UTF-8'}</a></b>{if $results[i].fullname} &mdash; {$results[i].fullname|escape:'html':'UTF-8'}{/if}<br /><small>{$results[i].bio|escape:'html':'UTF-8'}</small></p></li>
		{elseif $search_type == 'tag'}
			<li><p><b><a href='{$results[i].url}'>{$results[i].tag|escape:'html':'UTF-8'}</a></b></p></li>
		{/if}
		{/section}
	</ul>
	<br />

{include file='footer.tpl'}

