{include file='header.tpl'}

{include file='maxiprofile.tpl'}

{if $nowplaying|@count > 0}
<h3>{t}Now Playing:{/t}</h3>
    {section name=i loop=$nowplaying}
    <p><a href="{$nowplaying[i].trackurl|escape:'html':'UTF-8'}">{$nowplaying[i].track|escape:'html':'UTF-8'}</a> by <span class='artist-name'><a href='{$nowplaying[i].artisturl|escape:'html':'UTF-8'}'>{$nowplaying[i].artist|escape:'html':'UTF-8'}</a></span> with <span class='gobbler'>{$nowplaying[i].clientstr}</span>

<!-- {if $scrobbles[i].license > 0}{/if} we should put a download link here -->

</p>
    {/section}
{/if}

	<h3>{t plays=$scrobbles|@count}Latest %1 Plays:{/t}</h3>

<ol class="gobbles" about="{$me->id|escape:'html':'UTF-8'}" rev="gob:user">

{section name=i loop=$scrobbles}
	<li>
	<a href="{$scrobbles[i].trackurl|escape:'html':'UTF-8'}">{$scrobbles[i].track|escape:'html':'UTF-8'}</a> by <a about="{$scrobbles[i].id_artist|escape:'html':'UTF-8'}" typeof="mo:MusicArtist" property="foaf:name" rel="foaf:page"
					class="fn url" href="{$scrobbles[i].artisturl|escape:'html':'UTF-8'}"
					>{$scrobbles[i].artist|escape:'html':'UTF-8'}</a> {if $scrobbles[i].albumurl} on the album, <a href="{$scrobbles[i].albumurl|escape:'html':'UTF-8'}">{/if}
			&mdash; {$scrobbles[i].timehuman}
	</li>
{/section}
</ol>
{include file='footer.tpl'}
