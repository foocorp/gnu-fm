{include file='header.tpl'}

<h2 property="dc:title">{$me->name|escape:'html':'UTF-8'}'s statistics</h2>

{include file='maxiprofile.tpl'}

<h3 id="stats_by_artist">{$me->name|escape:'html':'UTF-8'}'s most played artists</h3>
<table class="stats_artists" about="{$me->id}">
	{section name=i loop=$user_playstats}
	<tr><td class="counts">{$user_playstats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_playstats[i].size}px" class="artist"></div></td><td><a
	href="{$user_playstats[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_playstats[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_playstats[i].artist|escape:"html":"UTF-8"}</a></td></tr>
	{/section}
</table>

<h3 id="stats_by_track">{$me->name|escape:'html':'UTF-8'}'s top tracks</h3>
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

<h3 id="stats_by_day">{$me->name|escape:'html':'UTF-8'}'s scrobbles by day</h3>
<table class="stats_artists" about="{$me->id}">
	{section name=i loop=$user_daystats}
	<tr><td class="counts">{$user_daystats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_daystats[i].size}px" class="artist"></div></td><td class="date">{$user_daystats[i].date}</td></tr>
	{/section}
</table>

<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{$me->name}'s statistics</h3>
<ul>
	<li><a href="#stats_by_artist">Most played artists</a></li>
	<li><a href="#stats_by_track">Top tracks</a></li>
	<li><a href="#stats_by_day">Scrobbles by day</a></li>
</ul>
<p><strong>More coming soon</strong></p>

{include file='footer.tpl'}
