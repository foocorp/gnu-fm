{include file='header.tpl'}

{if $flattr_uid}
{include file='flattr.tpl'}
{/if}

<div about="{$id}" typeof="mo:MusicArtist">

	<div class="vcard">

		{if $streamable}
		<div id='player-container'>
		{include file='player.tpl'}
		<script type="text/javascript">
			$(document).ready(function() {ldelim}
				{if isset($this_user)}
					playerInit(false, "{$this_user->getScrobbleSession()}", "{$this_user->getWebServiceSession()}", "{$radio_session}");
				{else}
					playerInit(false, false, false, "{$radio_session}");
				{/if}
			{rdelim});
		</script>
		</div>
		{/if}

		{if $image}
		<center><p id='artist_image' style='float: left; width: 256px;'>
			<img style='max-height: 256px; max-width: 256px;' src="{$image|escape:'htmlall'}" /><br />
			{if $homepage}<a href="{$homepage|escape:'htmlall'}">{$name|escape:'html':'UTF-8'}'s homepage</a>{/if}
		</p></center><br />
		{else}
			{if $homepage}<p style='clear: left;'><a href="{$homepage|escape:'htmlall'}">{t name=$name|escape:'html':'UTF-8'}%1's homepage{/t}</a></P>{/if}
		{/if}
		
		{if $bio_summary}
		<div class="note" id="bio" property="bio:olb" datatype="" style='clear: left;'>
		<h4>{t}Biography{/t}</h4>
		<p>{$bio_summary}</p>
			{if $bio_content}
				<a href='#' onclick='$("#show_more_bio").toggle(500); $("#bio_content").toggle(500);' id='show_more_bio'>{t}Show more...{/t}</a>
				<p id='bio_content' style='display: none;'>{$bio_content}</p>
			{/if}
		</div>
		{/if}
	</div>

	{include file='flattr-artist-button.tpl'}

	<h3>{t}Albums{/t}</h3>
	<ul>
		{section name=i loop=$albums}
		{if $albums[i]->name}
		<li about="{$albums[i]->id}" property="dc:title" content="{$albums[i]->name|escape:'html':'UTF-8'}" typeof="mo:Record" class="haudio">
					<a rel="foaf:page" href="{$albums[i]->getURL()}">{$albums[i]->name|escape:'html':'UTF-8'}</a>
		</li>{/if}
		{/section}	
		{if $add_album_link}<li><a href='{$add_album_link}'><strong>[{t}Add new album{/t}]</strong></a></li>{/if}
	</ul>

	<br />

	{if !empty($similarArtists)}
		<h3 style='text-align: center; clear: left;'>{t}Similar free artists{/t}</h3>
		<ul class="tagcloud">
		{section name=i loop=$similarArtists}
			<li style='font-size:{$similarArtists[i].size}'><a href='{$similarArtists[i].url}'>{$similarArtists[i].artist}</a></li>
		{/section}
		</ul>
	{/if}

	<br />

	{if !empty($tagcloud)}
		<h3 style='text-align: center; clear: left;'>{t}Tags used to describe this artist{/t}</h3>
		<ul class="tagcloud">
		{section name=i loop=$tagcloud}
			<li style='font-size:{$tagcloud[i].size}'><a href='{$tagcloud[i].pageurl}' title='{t uses=$tagcloud[i].count}This tag was used %1 times{/t}' rel='tag'>{$tagcloud[i].name}</a></li>
		{/section}
		</ul>
	{/if}

	<br />

</div>

{include file='footer.tpl'}

