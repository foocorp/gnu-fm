<h2 property="dc:title" class="fn" rel="foaf:page" rev="foaf:primaryTopic" resource="">
	<a href="{$artist->getURL()}">{$artist->name|escape:'html':'UTF-8'}</a>
	&#8212; 
	{$track->name|escape:'html':'UTF-8'}
	{if $edit_link}
		<a href="{$edit_link}"><small>[{t}Edit{/t}]</small></a>
	{/if}
</h2>
