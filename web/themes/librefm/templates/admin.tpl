{include file='header.tpl'}

<h2>Admin panel</h2>
<hr>
<b>Latest 10 Gobbles:</b>
<ul id="scrobbles" class="listcloud">
{section name=i loop=$scrobbles}
  <li>
    <dl>
      <dt><a href="artist.php?artist={$scrobbles[i].artist|stripslashes|urlencode}">
        {$scrobbles[i].artist|stripslashes}</a></dt>
      <dd>{$scrobbles[i].track|stripslashes}<br /><small>{$scrobbles[i].timehuman}</small></dd>
    </dl>
  </li>
{/section}
</ul>



{include file='footer.tpl'}
