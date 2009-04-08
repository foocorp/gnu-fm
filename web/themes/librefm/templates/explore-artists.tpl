{include file='header.tpl'}

<h2>Explore popular artists</h2>

<ul id="topartists" class="listcloud">
  {section name=popular loop=$topartists}
  <li>
    <dl>
      <dt><a href="artist.php?artist={$topartists[popular].artist|stripslashes|urlencode}">
        {$topartists[popular].artist|stripslashes}</a></dt>
      <dd>{$topartists[popular].c} gobbles</dd>
    </dl>
  </li>
  {/section}
</ul>


{include file='footer.tpl'}
