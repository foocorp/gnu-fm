{include file='header.tpl'}

<h2>{$tag|escape:'htmlall':'UTF-8'|capitalize}</h2>

<ul class="tagcloud">
{section name=i loop=$tagcloud}
	<li style='font-size:{$tagcloud[i].size}'><a href='{$tagcloud[i].pageurl}' title='This artist was tagged {$tagcloud[i].count} times' rel='tag'>{$tagcloud[i].name}</a></li>
{/section}
</ul>

{include file='footer.tpl'}

