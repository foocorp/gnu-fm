{include file='header.tpl' subheader='user-header.tpl'}

{if $me->bio}
<div id='bio'>
	<blockquote>{$me->bio|escape:'html':'UTF-8'}</blockquote>
</div>
{/if}
<br />
{if $nowplaying|@count > 0}
	<h3>{t}Now Playing:{/t}</h3>
	{section name=i loop=$nowplaying}
		<p><a href="{$nowplaying[i].trackurl|escape:'html':'UTF-8'}">{$nowplaying[i].track|escape:'html':'UTF-8'}</a> by <span class='artist-name'><a href='{$nowplaying[i].artisturl|escape:'html':'UTF-8'}'>{$nowplaying[i].artist|escape:'html':'UTF-8'}</a></span> with <span class='gobbler'>{if $nowplaying[i].clientname}<a href="{$nowplaying[i].clienturl|escape:'html':'UTF-8'}">{$nowplaying[i].clientname|escape:'html':'UTF-8'}</a>{else}{$nowplaying[i].client|escape:'html':'UTF-8'} (unknown, <a href="http://bugs.foocorp.net/projects/librefm/wiki/Clients">please tell us what this is</a>){/if}</span> {if $nowplaying[i].loved}<img src='{$img_url}/love-small.png' alt='{t}Loved{/t}' title='{t}Loved{/t}' />{/if}</p>
	{/section}
{/if}

<h3>{t plays=$scrobbles|@count}Latest %1 Plays:{/t}</h3>

<ol class="gobbles" about="{$me->id|escape:'html':'UTF-8'}" rev="gob:user">

{section name=i loop=$scrobbles}
	<li>
	<a href="{$scrobbles[i].trackurl|escape:'html':'UTF-8'}">{$scrobbles[i].track|escape:'html':'UTF-8'}</a> by <a about="{$scrobbles[i].id_artist|escape:'html':'UTF-8'}" typeof="mo:MusicArtist" property="foaf:name" rel="foaf:page"
					class="fn url" href="{$scrobbles[i].artisturl|escape:'html':'UTF-8'}"
					>{$scrobbles[i].artist|escape:'html':'UTF-8'}</a>
			&mdash; {$scrobbles[i].timehuman} {if $scrobbles[i].loved}<img src='{$img_url}/love-small.png' alt='{t}Loved{/t}' title='{t}Loved{/t}' />{/if}
	</li>
{/section}
</ol>

{if !empty($lovedArtists)}
	<br />
	<h3 style='text-align: center; clear: left;'>{t name=$me->name}Free artists that %1 loves{/t}</h3>
	<ul class="tagcloud">
		{section name=i loop=$lovedArtists}
			<li style='font-size:{$lovedArtists[i].size}'><a href='{$lovedArtists[i].pageurl}'>{$lovedArtists[i].name}</a></li>
		{/section}
	</ul>
{/if}

{if !empty($recommendedArtists)}
	<br />
	<h3 style='text-align: center; clear: left;'>{t name=$me->name}Free artists that %1 might like{/t}</h3>
	<ul class="tagcloud">
		{foreach from=$recommendedArtists item=artist}
			<li style='font-size:{$artist.size}'><a href='{$artist.url}'>{$artist.artist}</a></li>
		{/foreach}
	</ul>
{/if}

{include file='footer.tpl'}
