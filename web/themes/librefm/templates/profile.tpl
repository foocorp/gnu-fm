{include file='header.tpl'}

<h2 property="dc:title">{$user}'{if $user|substr:-1 != 's'}s{/if} profile</h2>
<dl about="{$id}" typeof="foaf:Agent" class="user vcard">
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
	<dd class='avatar'>
		<!-- Avatar placeholder  -->
		<img rel="foaf:depiction" src="{$avatar}" class="photo" alt="avatar" />
	</dd>
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


{if $nowplaying|@count > 0}
<h3>Now Playing:</h3>
<dl class='now-playing'>
    {section name=i loop=$nowplaying}
    <dt class='track-name'>{$nowplaying[i].track}</dt>
    <dd>by <span class='artist-name'><a href='{$nowplaying[i].artisturl}'>{$nowplaying[i].artist}</a></span></dd>
    <dd>with <span class='gobbler'>{$nowplaying[i].clientstr}</span></dd>
    {/section}
</dl>
{/if}

<h3>Latest {$scrobbles|@count} Gobbles:</h3>

<ul class="gobbles" about="{$id}" rev="gob:user">
{section name=i loop=$scrobbles}

	<li about="{$scrobbles[i].id}" typeof="gob:ScrobbleEvent" rel="gob:track_played">
		<div about="{$scrobbles[i].id_track}" typeof="mo:Track" class="haudio">
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
   
</div></div>

    <div class="yui-u" id="sidebar">
    <div style="padding: 10px;">
      <h3>User's favorite artists</h3>
<ul class="tagcloud">
{section name=i loop=$user_tagcloud}
  <li style='font-size:{$user_tagcloud[i].size}'><a href='/artist/{$user_tagcloud[i].artist|urlencode}' rel='tag'>{$user_tagcloud[i].artist|stripslashes}</a></li>
{/section}
  </ul>
{include file='footer.tpl'}
