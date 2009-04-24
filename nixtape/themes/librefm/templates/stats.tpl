{include file='header.tpl'}

<h2 property="dc:title">{$user|escape:'html':'UTF-8'}'{if $user|substr:-1 != 's'}s{/if} statistics</h2>
<div about="{$id|escape:'html':'UTF-8'}" typeof="foaf:Agent" class="user vcard">

	<div class="avatar" rel="foaf:depiction">
		<!-- Avatar placeholder  -->
		<img src="{$avatar|escape:'html':'UTF-8'}" alt="avatar" class="photo" />
	</div>

	{if $isme}
	<a class="edit" href="{$base_url}/edit_profile.php">[edit]</a>
	{/if}
	
	<dl>
		<dt>
			<span class="fn" property="foaf:name">{$fullname|escape:'html':'UTF-8'}</span>
			<span rel="foaf:holdsAccount" rev="sioc:account_of">
				<span about="{$acctid|escape:'html':'UTF-8'}" typeof="sioc:User">
					(<span class="nickname" property="foaf:accountName">{$user|escape:'html':'UTF-8'}</span>)
					<span rel="foaf:accountServiceHomepage" resource="{$base_url}"></span>
					<span rel="foaf:accountProfilePage" rev="foaf:topic" resource=""></span>
				</span>
			</span>
		</dt>
		{if $homepage}
		<dd>
			<a href="{$homepage|escape:'html':'UTF-8'}" rel="me foaf:homepage" class="url">{$homepage|escape:'html':'UTF-8'}</a>
		</dd>
		{/if}
	</dl>

	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:page" rev="foaf:primaryTopic" resource="" />
</div>

<h3 id="stats_by_artist">{$user}'{if $user|substr:-1 != 's'}s{/if} most played artists</h3>
<table class="stats_artists" about="{$id}">
	{section name=i loop=$user_playstats}
	<tr><td class="counts">{$user_playstats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_playstats[i].size}px" class="artist"></div></td><td><a
	href="{$user_playstats[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_playstats[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_playstats[i].artist|escape:"html":"UTF-8"}</a></td></tr>
	{/section}
</table>

<h3 id="stats_by_day">{$user}'{if $user|substr:-1 != 's'}s{/if} scrobbles by day</h3>
<table class="stats_artists" about="{$id}">
	{section name=i loop=$user_daystats}
	<tr><td class="counts">{$user_daystats[i].count}</td><td class="bar" style="width: {$stat_barwidth}px"><div style="width:{$user_daystats[i].size}px" class="artist"></div></td><td class="date">{$user_daystats[i].date}</td></tr>
	{/section}
</table>

<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{$user}'{if $user|substr:-1 != 's'}s{/if} statistics</h3>
<ul>
	<li><a href="#stats_by_artist">Most played artists</a></li>
	<li><a href="#stats_by_day">Scrobbles by day</a></li>
</ul>
<p><strong>More coming soon</strong></p>

{include file='footer.tpl'}
