{include file='header.tpl'}

<h2>{$name}</h2><br />
<h3>Artist: {$artist}</h3><br />
<h3>Album: {$album}</h3>

<ul id="tracks">
  <li>
      Duration: {$duration}
  </li>
  <li>
      Playcount: {$playcount}
  </li>
  <li>
      Listeners: {$listeners}
  </li>
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
