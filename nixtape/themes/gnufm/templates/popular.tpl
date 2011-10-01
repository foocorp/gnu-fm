	<h3>{t}Explore popular free artists{/t}</h3>
	<ul class="tagcloud">
	{section name=i loop=$tagcloud}
		<li style='font-size:{$tagcloud[i].size}'><a href='{$tagcloud[i].pageurl}' title='{t loves=$tagcloud[i].count}This artist was loved %1 times{/t}' rel='tag'>{$tagcloud[i].artist}</a></li>
	{/section}
	</ul>
