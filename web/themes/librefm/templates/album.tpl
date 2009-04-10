{include file='header.tpl'}

<h2>{$artist} - {$name}</h2>

<ul id="tracks">
  {section name=i loop=$tracks}
  <li>
      <a href="track.php?artist={$artist|urlencode}&track={$tracks[i]->name|urlencode}">{$tracks[i]->name}</a>
  </li>
  {/section}
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
