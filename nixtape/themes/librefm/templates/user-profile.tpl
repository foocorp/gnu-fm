{include file='header.tpl'}

{include file='maxiprofile.tpl'}

{if $nowplaying|@count > 0}
<h3>Now Playing:</h3>
<!-- We should try to make this list work like the gobbles list. -->
<dl class='now-playing'>
    {section name=i loop=$nowplaying}
    <dt class='track-name {if $scrobbles[i].license > 0}libre{/if}'><a href="{$nowplaying[i].trackurl|escape:'html':'UTF-8'}">{$nowplaying[i].track|escape:'html':'UTF-8'}</a></dt>
    <dd>by <span class='artist-name'><a href='{$nowplaying[i].artisturl|escape:'html':'UTF-8'}'>{$nowplaying[i].artist|escape:'html':'UTF-8'}</a></span></dd>
    <dd>with <span class='gobbler'>{$nowplaying[i].clientstr}</span></dd>
    {/section}
</dl>
{/if}

<div about="[_:seq1]" typeof="rdf:Seq" rev="rss:items">
	<h3 typeof="rss:channel" property="rss:title" rel="rss:link" resource="#latest_plays" id="latest_plays" content="{$me->name|escape:'html':'UTF-8'}'s Latest Plays">Latest {$scrobbles|@count} Plays:</h3>
</div>

<ul class="gobbles" about="{$me->id|escape:'html':'UTF-8'}" rev="gob:user">
{section name=i loop=$scrobbles}

	<li class="play {if $scrobbles[i].license > 0}libre{/if}" about="{$scrobbles[i].id|escape:'html':'UTF-8'}" typeof="rss:item gob:ScrobbleEvent" rel="gob:track_played">
		<div about="{$scrobbles[i].id_track|escape:'html':'UTF-8'}" typeof="mo:Track" class="haudio">
			<div rev="mo:track">
				<div about="{$scrobbles[i].id_album|escape:'html':'UTF-8'}" typeof="mo:Record"{if $scrobbles[i].album} property="dc:title" content="{$scrobbles[i].album|escape:'html':'UTF-8'}"{/if}>
					{if $scrobbles[i].albumurl}<a rel="foaf:page" href="{$scrobbles[i].albumurl|escape:'html':'UTF-8'}">{/if}
						<span{if $scrobbles[i].album_image} rel="foaf:depiction"{/if}{if $scrobbles[i].albumurl} about="{$scrobbles[i].id_album|escape:'html':'UTF-8'}"{/if}>
							<img height="50" width="50" 
								src="{if !$scrobbles[i].album_image}/i/qm50.png{else}{$scrobbles[i].album_image|escape:'html':'UTF-8'}{/if}" 
								class="albumart{if !$scrobbles[i].album_image} photo{/if}" 
								{if $scrobbles[i].album}title="{$scrobbles[i].album|escape:'html':'UTF-8'}" alt="Album: {$scrobbles[i].album|escape:'html':'UTF-8'}"{else}alt="Unknown album"{/if}  />
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
			<span about="{$scrobbles[i].id|escape:'html':'UTF-8'}" property="rss:link" content="{$scrobbles[i].trackurl|escape:'html':'UTF-8'}">
				<span property="rss:description" content="{if $scrobbles[i].album}{$scrobbles[i].album}{else}Unknown album{/if}">
					<span property="rss:title" content="{$scrobbles[i].artist|escape:'html':'UTF-8'}: {$scrobbles[i].track|escape:'html':'UTF-8'}" rev="rdf:_{$smarty.section.i.index_next}" resource="[_:seq1]"></span>
				</span>
			</span>
		</div>
	</li>
{/section}
</ul>
{include file='footer.tpl'}
