{include file='header.tpl'}

<h2>{$name}</h2><br />
<b>Artist: {$artist}</b><br />
<b>Album: {$album}</b>

<ul id="tracks">
{if isset($duration)}<li> Duration: {$duration}</li>{/if}
  <li>
      Playcount: {$playcount}
  </li>
  <li>
      Listeners: {$listeners}
  </li>
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
