{include file='header.tpl'}

<h2>{$track->name}</h2><br />

{include file='player.tpl'}<br />

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
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
