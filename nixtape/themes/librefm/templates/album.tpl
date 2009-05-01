{include file='header.tpl'}

<h2><a href="{$artist->getURL()}">{$artist->name}</a> - {$name}</h2>

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
