{include file='header.tpl'}

<h2 property="dc:title">{t name=$me->name|escape:'html':'UTF-8'}%1's statistics{/t}</h2>

{include file='maxiprofile.tpl'}

<h3>Total tracks: {$totaltracks}</h3>

<h4 id="stats_by_artist">{t name=$me->name|escape:'html':'UTF-8'}%1's most played artists{/t}</h4>

<ul class="stats_artists" about="{$me->id}">
	{section name=i loop=$user_playstats}
	<li><a
	href="{$user_playstats[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_playstats[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_playstats[i].artist|escape:"html":"UTF-8"}</a> &mdash; <div style="width:{$user_playstats[i].size}px; background-color: red;" class="artist">{$user_playstats[i].count}</div></li>
	{/section}
</ul>

<h4 id="stats_by_track">{t name=$me->name|escape:'html':'UTF-8'}%1's top tracks{/t}</h4>
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

<h4 id="stats_by_day">{t name=$me->name|escape:'html':'UTF-8'}%1's scrobbles by day{/t}</h4>
<ul class="stats_artists" about="{$me->id}">
	{section name=i loop=$user_daystats}
	<li>{$user_daystats[i].date} &mdash; <div style="width:{$user_daystats[i].size}px; background-color: red;" class="artist">{$user_daystats[i].count}</div>
	{/section}
</ul>

<ul>
	<li><a href="#stats_by_artist">{t}Most played artists{/t}</a></li>
	<li><a href="#stats_by_track">{t}Top tracks{/t}</a></li>
	<li><a href="#stats_by_day">{t}Scrobbles by day{/t}</a></li>
</ul>

{include file='footer.tpl'}
