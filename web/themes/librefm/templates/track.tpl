{include file='header.tpl'}

<h2>{$track->name}</h2><br />

{include file='player.tpl'}<br />

<b>Artist: {$track->artist_name}</b><br />
<b>Album: {$track->album_name}</b><br />
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
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
