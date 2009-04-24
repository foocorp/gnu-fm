{include file='header.tpl'}

<h2 property="dc:title">{$user|escape:'html':'UTF-8'}'{if $user|substr:-1 != 's'}s{/if} profile</h2>
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
		<dd rel="foaf:based_near">
			<span{if $location_uri} about="{$location_uri|escape:'html':'UTF-8'}"{/if} class="label" property="rdfs:comment">{$location|escape:'html':'UTF-8'}</span>
		</dd>
		<dd class="note" property="bio:olb">{$bio|escape:'html':'UTF-8'}</dd>
	</dl>

	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:page" rev="foaf:primaryTopic" resource="" />

</div>

{if $nowplaying|@count > 0}
<h3>Now Playing:</h3>
<!-- We should try to make this list work like the gobbles list. -->
<dl class='now-playing'>
    {section name=i loop=$nowplaying}
    <dt class='track-name'>{$nowplaying[i].track|escape:'html':'UTF-8'}</dt>
    <dd>by <span class='artist-name'><a href='{$nowplaying[i].artisturl|escape:'html':'UTF-8'}'>{$nowplaying[i].artist|escape:'html':'UTF-8'}</a></span></dd>
    <dd>with <span class='gobbler'>{$nowplaying[i].clientstr}</span></dd>
    {/section}
</dl>
{/if}

<div about="[_:seq1]" typeof="rdf:Seq" rev="rss:items">
	<h3 typeof="rss:channel" property="rss:title" rel="rss:link" resource="#latest_plays" id="latest_plays" content="{$user|escape:'html':'UTF-8'}'{if $user|substr:-1 != 's'}s{/if} Latest Plays">Latest {$scrobbles|@count} Plays:</h3>
</div>

<ul class="gobbles" about="{$id|escape:'html':'UTF-8'}" rev="gob:user">
{section name=i loop=$scrobbles}

	<li about="{$scrobbles[i].id|escape:'html':'UTF-8'}" typeof="rss:item gob:ScrobbleEvent" rel="gob:track_played">
		<div about="{$scrobbles[i].id_track|escape:'html':'UTF-8'}" typeof="mo:Track" class="haudio">
			<div rev="mo:track">
				<div about="{$scrobbles[i].id_album|escape:'html':'UTF-8'}" typeof="mo:Record"{if $scrobbles[i].album} property="dc:title" content="{$scrobbles[i].album|escape:'html':'UTF-8'}"{/if}>
					{if $scrobbles[i].albumurl}<a rel="foaf:page" href="{$scrobbles[i].albumurl|escape:'html':'UTF-8'}">{/if}
						<span{if $scrobbles[i].album_image != '/i/qm50.png'} rel="foaf:depiction"{/if}{if $scrobbles[i].albumurl} about="{$scrobbles[i].id_album|escape:'html':'UTF-8'}"{/if}>
							<img height="50" width="50" src="{if $scrobbles[i].album_image != '/i/qm50.png'}{$scrobbles[i].album_image|escape:'html':'UTF-8'}{else}{$base_url}{$scrobbles[i].album_image|escape:'html':'UTF-8'}{/if}" class="albumart{if $scrobbles[i].album_image != '/i/qm50.png'} photo{/if}" {if $scrobbles[i].album}title="{$scrobbles[i].album|escape:'html':'UTF-8'}" alt="Album: {$scrobbles[i].album|escape:'html':'UTF-8'}"{else}alt="Unknown album"{/if}  />
						</span>
					{if $scrobbles[i].albumurl}</a>{/if}
				</div>
			</div>
			<div rel="foaf:maker" class="contributor vcard">
				<a about="{$scrobbles[i].id_artist|escape:'html':'UTF-8'}" typeof="mo:MusicArtist" property="foaf:name" rel="foaf:page"
					class="fn url" href="{$scrobbles[i].artisturl|escape:'html':'UTF-8'}"
					>{$scrobbles[i].artist|escape:'html':'UTF-8'}</a>
			</div>
			<div><a class="fn" property="dc:title" rel="foaf:page" href="{$scrobbles[i].trackurl|escape:'html':'UTF-8'}">{$scrobbles[i].track|escape:'html':'UTF-8'}</a></div>
			<small about="{$scrobbles[i].id|escape:'html':'UTF-8'}" property="dc:date" content="{$scrobbles[i].timeiso}" datatype="xsd:dateTime">{$scrobbles[i].timehuman}</small>
		</div>
		<span about="{$scrobbles[i].id|escape:'html':'UTF-8'}" property="rss:link" content="{$scrobbles[i].trackurl|escape:'html':'UTF-8'}">
			<span property="rss:description" content="{if $scrobbles[i].album}{$scrobbles[i].album}{else}Unknown album{/if}">
				<span property="rss:title" content="{$scrobbles[i].artist|escape:'html':'UTF-8'}: {$scrobbles[i].track|escape:'html':'UTF-8'}" rev="rdf:_{$smarty.section.i.index_next}" resource="[_:seq1]"></span>
			</span>
		</span>
	</li>
{/section}
</ul>

<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{$user}'{if $user|substr:-1 != 's'}s{/if} top artists</h3>
<ul class="tagcloud" about="{$id}">
	{section name=i loop=$user_tagcloud}
	<li style="font-size:{$user_tagcloud[i].size}"><a
	href="{$user_tagcloud[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_tagcloud[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_tagcloud[i].artist|escape:"html":"UTF-8"}</a></li>
	{/section}
</ul>

{include file='footer.tpl'}
