{include file='header.tpl'}

<h2 property="dc:title">{t name=$me->name|escape:'html':'UTF-8'}%1's statistics{/t}</h2>

{include file='maxiprofile.tpl'}

<h3 id="stats_by_artist">{t name=$me->name|escape:'html':'UTF-8'}%1's most played artists{/t}</h3>
<table class="stats_artists" about="{$me->id}">
	{section name=i loop=$user_playstats}
	<tr><td class="counts">{$user_playstats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_playstats[i].size}px" class="artist"></div></td><td><a
	href="{$user_playstats[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_playstats[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_playstats[i].artist|escape:"html":"UTF-8"}</a></td></tr>
	{/section}
</table>

<h3 id="stats_by_track">{t name=$me->name|escape:'html':'UTF-8'}%1's top tracks{/t}</h3>
<table class="stats_artists" about="{$me->id}">
{section name=i loop=$toptracks}
	<tr>
		<td class="counts">{$toptracks[i].c}</td>
		<td class="bar" style="width: {$toptracks[i].width}px">
			<div style="width:{$toptracks[i].width}px" class="track"></div>
		</td>
		<td>
			<a href="{$toptracks[i].artisturl|escape:'html':'UTF-8'}">{$toptracks[i].artist|escape:'html':'UTF-8'}</a>
		</td>
		<td>
			<a href="{$toptracks[i].trackurl|escape:'html':'UTF-8'}">{$toptracks[i].track|escape:'html':'UTF-8'}</a>
		</td>
	</tr>
{/section}
</table>

<h3 id="stats_by_day">{t name=$me->name|escape:'html':'UTF-8'}%1's scrobbles by day{/t}</h3>
<table class="stats_artists" about="{$me->id}">
	{section name=i loop=$user_daystats}
	<tr><td class="counts">{$user_daystats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_daystats[i].size}px" class="artist"></div></td><td class="date">{$user_daystats[i].date}</td></tr>
	{/section}
</table>

<ul>
	<li><a href="#stats_by_artist">{t}Most played artists{/t}</a></li>
	<li><a href="#stats_by_track">{t}Top tracks{/t}</a></li>
	<li><a href="#stats_by_day">{t}Scrobbles by day{/t}</a></li>
</ul>

{include file='footer.tpl'}
