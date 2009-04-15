{include file='header.tpl'}

<h2 property="dc:title">{$user}'{if $user|substr:-1 != 's'}s{/if} profile</h2>
<div about="{$id}" typeof="foaf:Agent" class="user vcard">

	<div class="avatar" rel="foaf:depiction">
		<!-- Avatar placeholder  -->
		<img src="{$avatar|htmlentities}" alt="avatar" class="photo" />
	</div>
	
	<dl>
		<dt>
			<span class="fn" property="foaf:name">{$fullname|utf8_encode}</span>
			<span rel="foaf:holdsAccount" rev="sioc:account_of">
				<span about="{$acctid}" typeof="sioc:User">
					(<span class="nickname" property="foaf:accountName">{$user}</span>)
					<span rel="foaf:accountServiceHomepage" resource="{$base_url}"></span>
					<span rel="foaf:homepage" rev="foaf:primaryTopic" resource=""></span>
				</span>
			</span>
		</dt>
		{if $homepage}
		<dd>
			<a href="{$homepage}" rel="foaf:homepage" rev="foaf:primaryTopic" class="url">{$homepage}</a>
		</dd>
		{/if}
		<dd rel="foaf:based_near">
			<span class="label" property="rdfs:label">{$location}</span>
		</dd>
		<dd class="note" property="bio:olb">{$bio}</dd>
	</dl>
</div>

<hr style="border: none; clear: both;" />

{if $nowplaying|@count > 0}
<h3>Now Playing:</h3>
<!-- We should try to make this list work like the gobbles list. -->
<dl class='now-playing'>
    {section name=i loop=$nowplaying}
    <dt class='track-name'>{$nowplaying[i].track}</dt>
    <dd>by <span class='artist-name'><a href='{$nowplaying[i].artisturl}'>{$nowplaying[i].artist}</a></span></dd>
    <dd>with <span class='gobbler'>{$nowplaying[i].clientstr}</span></dd>
    {/section}
</dl>
{/if}

<h3>Latest {$scrobbles|@count} Plays:</h3>

<ul class="gobbles" about="{$id}" rev="gob:user">
{section name=i loop=$scrobbles}

	<li about="{$scrobbles[i].id}" typeof="gob:ScrobbleEvent" rel="gob:track_played">
		<div about="{$scrobbles[i].id_track}" typeof="mo:Track" class="haudio">
			<div rev="mo:track">
				<div typeof="mo:Record" property="dc:title" content="{$scrobbles[i].album}">
					<span{if $scrobbles[i].albumart != '/i/qm50.png'} rel="foaf:depiction"{/if}><img src="{$scrobbles[i].albumart}" class="albumart{if $scrobbles[i].albumart != '/i/qm50.png'} photo{/if}" title="{$scrobbles[i].album}" alt="Album: {$scrobbles[i].album}"  /></span>
				</div>
			</div>
			<div rel="foaf:maker" class="contributor vcard">
				<a about="{$scrobbles[i].id_artist}" typeof="mo:MusicArtist" property="foaf:name" rel="foaf:page"
					class="fn url" href="{$scrobbles[i].artisturl}"
					>{$scrobbles[i].artist}</a>
			</div>
			<div><a class="fn" property="dc:title" rel="foaf:page" href="{$scrobbles[i].trackurl}">{$scrobbles[i].track}</a></div>
			<small about="{$scrobbles[i].id}" property="dc:date" content="{$scrobbles[i].timeiso}" datatype="xsd:dateTime">{$scrobbles[i].timehuman}</small>
		</div>
	</li>
{/section}
</ul>

<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{$user}'{if $user|substr:-1 != 's'}s{/if} top artists</h3>
<ul class="tagcloud" about="{$id}">
	{section name=i loop=$user_tagcloud}
	<li style="font-size:{$user_tagcloud[i].size}"><a href="/artist/{$user_tagcloud[i].artist|urlencode|htmlentities}" rel="{if $user_tagcloud[i].size|substr:-5 == 'large'}foaf:interest {/if}tag">{$user_tagcloud[i].artist|htmlentities}</a></li>
	{/section}
</ul>

{include file='footer.tpl'}
