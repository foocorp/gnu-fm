{include file='header.tpl'}

<h2>{$track->name}</h2><br />

{include file='player.tpl'}
<script type="text/javascript">
	var playlist = [{ldelim}"artist" : "{$track->artist_name}", "album" : "{$track->album_name}", "track" : "{$track->name}", "url" : "{$track->streamurl}"{rdelim}];
	{if isset($this_user)}
	playerInit(playlist, "{$this_user->getScrobbleSession()}", false);
	{else}
	playerInit(playlist, false, false);
	{/if}
</script>
<br />


<b>Artist: <a href="{$artisturl}">{$track->artist_name}</a></b><br />
<b>Album: <a href="{$albumurl}">{$track->album_name}</a></b><br />
{if $track->mbid != ""}
<b>MusicBrainz ID: <a href="http://musicbrainz.org/track/{$track->mbid}.html">{$track->mbid}</a></b><br />
{/if}
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

      Albums containing this track:
{section name=i loop=$albums}
<span{if $albums[i]->image != false} about="{$albums[i]->id}"{/if}>
<img class="album photo" {if $albums[i]->image == false} src="{$base_url}/i/qm160.png"{else}src="{$albums[i]->image}"{/if}
 alt="{$albums[i]->name|escape:'html':'UTF-8'}"title="{$albums[i]->name|escape:'html':'UTF-8'}" width="160" />
</span>
{/section}

  </li>
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
