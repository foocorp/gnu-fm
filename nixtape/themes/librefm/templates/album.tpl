{include file='header.tpl'}

<h2><a href="{$artist->getURL()}">{$artist->name}</a> - {$name}</h2>

<a rel="foaf:page" href="{$album->getURL()}">
<span{if $album->image != false} about="{$album->id}" rel="foaf:depiction"{/if}>
<img class="album photo" {if $album->image == false} src="{$base_url}/i/qm160.png"{else}src="{$album->image}"{/if}
 alt="{$album->name|escape:'html':'UTF-8'}"title="{$album->name|escape:'html':'UTF-8'}" width="160" />
</span>
</a>

{include file='player.tpl'}

<script type="text/javascript">
	var playlist = [
	{section name=i loop=$tracks}
		{ldelim} "artist" : "{$tracks[i]->artist_name}", "album" : "{$tracks[i]->album_name}", "track" : "{$tracks[i]->name}", "url" : "{$tracks[i]->streamurl}" {rdelim},
	{/section}
	];

	{if isset($this_user)}
	playerInit(playlist, "{$this_user->getScrobbleSession()}", false);
	{else}
	playerInit(playlist, false, false);
	{/if}
</script>

<ul id="tracks">
	{section name=i loop=$tracks}
	<li>
		<a href="{$tracks[i]->getURL()}">{$tracks[i]->name}</a>
	</li>
	{/section}
</ul>

<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
