{include file='header.tpl'}

<h2 property="dc:title">{$me->name|escape:'html':'UTF-8'}'s recent tracks</h2>

{include file='maxiprofile.tpl'}

<div about="[_:seq1]" typeof="rdf:Seq" rev="rss:items">
	<h3 typeof="rss:channel" property="rss:title" rel="rss:link" resource="#latest_plays" id="latest_plays" content="{$me->name|escape:'html':'UTF-8'}'s Latest Plays">Latest {$scrobbles|@count} Plays:</h3>
</div>

<ol class="gobbles" about="{$me->id|escape:'html':'UTF-8'}" rev="gob:user">

{section name=i loop=$scrobbles}
	<li>
	<a href="{$scrobbles[i].trackurl|escape:'html':'UTF-8'}">{$scrobbles[i].track|escape:'html':'UTF-8'}</a> by <a about="{$scrobbles[i].id_artist|escape:'html':'UTF-8'}" typeof="mo:MusicArtist" property="foaf:name" rel="foaf:page"
					class="fn url" href="{$scrobbles[i].artisturl|escape:'html':'UTF-8'}"
					>{$scrobbles[i].artist|escape:'html':'UTF-8'}</a> {if $scrobbles[i].albumurl} on the album, <a href="{$scrobbles[i].albumurl|escape:'html':'UTF-8'}">{$scrobbles[i].album|escape:'html':'UTF-8'}</a>{/if}
			&mdash; {$scrobbles[i].timehuman}
	</li>
{/section}
</ol>
{include file='footer.tpl'}
