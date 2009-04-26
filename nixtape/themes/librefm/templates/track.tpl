{include file='header.tpl'}

<h2>{$track->name}</h2><br />

{include file='player.tpl'}
<script type="text/javascript">
	var playlist = [{ldelim}"artist" : "{$track->artist_name}", "album" : "{$track->album_name}", "track" : "{$track->name}", "url" : "{$track->streamurl}"{rdelim}];
	{if isset($u_user)}
	playerInit(playlist, "{$u_user->getScrobbleSession()}");
	{else}
	playerInit(playlist, false);
	{/if}
</script>
<br />


<b>Artist: <a href="{$artisturl}">{$track->artist_name}</a></b><br />
<b>Album: <a href="{$albumurl}">{$track->album_name}</a></b><br />
<br />
<a href="{$track->licenseurl}"><img src="{$base_url}/themes/librefm/images/licenses/{$track->license}.png" /></a>

<ul id="tracks">
{if !empty($track->duration)}<li> Duration: {$track->duration}</li>{/if}
  <li>
      Playcount: {$track->getPlayCount()}
  </li>
  <li>
      Listeners: {$track->getListenerCount()}
  </li>
  <li>
      Other Albums containing this track: {$otheralbum}
  </li>
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
