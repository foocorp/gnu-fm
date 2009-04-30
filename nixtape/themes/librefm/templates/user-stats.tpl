{include file='header.tpl'}

<h2 property="dc:title">{$user|escape:'html':'UTF-8'}'s statistics</h2>

{include file='maxiprofile.tpl'}

<h3 id="stats_by_artist">{$user}'s most played artists</h3>
<table class="stats_artists" about="{$id}">
	{section name=i loop=$user_playstats}
	<tr><td class="counts">{$user_playstats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_playstats[i].size}px" class="artist"></div></td><td><a
	href="{$user_playstats[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_playstats[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_playstats[i].artist|escape:"html":"UTF-8"}</a></td></tr>
	{/section}
</table>

<h3 id="stats_by_day">{$user}'s scrobbles by day</h3>
<table class="stats_artists" about="{$id}">
	{section name=i loop=$user_daystats}
	<tr><td class="counts">{$user_daystats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_daystats[i].size}px" class="artist"></div></td><td class="date">{$user_daystats[i].date}</td></tr>
	{/section}
</table>

<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{$user}'s statistics</h3>
<ul>
	<li><a href="#stats_by_artist">Most played artists</a></li>
	<li><a href="#stats_by_day">Scrobbles by day</a></li>
</ul>
<p><strong>More coming soon</strong></p>

{include file='footer.tpl'}
