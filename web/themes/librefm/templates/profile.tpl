{include file='header.tpl'}

<h2 property="dc:title">{$user}'{if $user|substr:-1 != 's'}s{/if} profile</h2>
<div about="{$id}" typeof="foaf:Agent" class="user vcard">

	<div class="avatar" rel="foaf:depiction">
		<!-- Avatar placeholder  -->
		<img src="{$avatar|htmlentities}" alt="avatar" class="photo" />
	</div>

	{if $isme}
	<a class="edit" href="{$base_url}/edit_profile.php">[edit]</a>
	{/if}
	
	<dl>
		<dt>
			<span class="fn" property="foaf:name">{$fullname}</span>
			<span rel="foaf:holdsAccount" rev="sioc:account_of">
				<span about="{$acctid}" typeof="sioc:User">
					(<span class="nickname" property="foaf:accountName">{$user}</span>)
					<span rel="foaf:accountServiceHomepage" resource="{$base_url}"></span>
					<span rel="foaf:accountHomepage" rev="foaf:topic" resource=""></span>
				</span>
			</span>
		</dt>
		{if $homepage}
		<dd>
			<a href="{$homepage}" rel="me foaf:homepage" rev="foaf:primaryTopic" class="url">{$homepage}</a>
		</dd>
		{/if}
		<dd rel="foaf:based_near">
			<span{if $location_uri} about="{$location_uri}"{/if} class="label" property="rdfs:comment">{$location}</span>
		</dd>
		<dd class="note" property="bio:olb">{$bio}</dd>
	</dl>

	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:page" rev="foaf:primaryTopic" resource="" />

</div>

{if $nowplaying|@count > 0}
<h3>Now Playing:</h3>
<!-- We should try to make this list work like the gobbles list. -->
<dl class='now-playing'>
    {section name=i loop=$nowplaying}
    <dt class='track-name'>{$nowplaying[i].track|htmlentities}</dt>
    <dd>by <span class='artist-name'><a href='{$nowplaying[i].artisturl}'>{$nowplaying[i].artist|htmlentities}</a></span></dd>
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
				<div about="{$scrobbles[i].id_album}" typeof="mo:Record"{if $scrobbles[i].album} property="dc:title" content="{$scrobbles[i].album|htmlentities}"{/if}>
					{if $scrobbles[i].albumurl}<a rel="foaf:page" href="{$scrobbles[i].albumurl}">{/if}
						<span{if $scrobbles[i].album_image != '/i/qm50.png'} rel="foaf:depiction"{/if}{if $scrobbles[i].albumurl} about="{$scrobbles[i].id_album}"{/if}>
							<img height="50" width="50" src="{$scrobbles[i].album_image}" class="albumart{if $scrobbles[i].album_image != '/i/qm50.png'} photo{/if}" {if $scrobbles[i].album}title="{$scrobbles[i].album|htmlentities}" alt="Album: {$scrobbles[i].album|htmlentities}"{else}alt="Unknown album"{/if}  />
						</span>
					{if $scrobbles[i].albumurl}</a>{/if}
				</div>
			</div>
			<div rel="foaf:maker" class="contributor vcard">
				<a about="{$scrobbles[i].id_artist}" typeof="mo:MusicArtist" property="foaf:name" rel="foaf:page"
					class="fn url" href="{$scrobbles[i].artisturl}"
					>{$scrobbles[i].artist|htmlentities}</a>
			</div>
			<div><a class="fn" property="dc:title" rel="foaf:page" href="{$scrobbles[i].trackurl}">{$scrobbles[i].track|htmlentities}</a></div>
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
	<li style="font-size:{$user_tagcloud[i].size}"><a
	href="{$user_tagcloud[i].pageurl}" rel="{if $user_tagcloud[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_tagcloud[i].artist|escape:"html":"UTF-8"}</a></li>
	{/section}
</ul>

{include file='footer.tpl'}
