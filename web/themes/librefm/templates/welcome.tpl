{include file='header.tpl'}

<h2>Welcome</h2>
<p><strong><span class='vcard fn org'>libre.fm</span></strong> is a free network service that will allow users to share their
musical tastes with other people.</p>

<h3>Now playing</h3>

<ul id="nowplaying" class="listcloud">
  {section name=np loop=$nowplaying}
   <li>
    <dl>
      <dt><a href="artist.php?artist={$nowplaying[np].artist|stripslashes|urlencode}">
        {$nowplaying[np].artist|stripslashes|htmlspecialchars}</a></dt>
      <dd>{$nowplaying[np].track|stripslashes|htmlspecialchars}</dd>
      <dd>{$nowplaying[np].username|stripslashes|htmlspecialchars}</dd>
      <dd>{$nowplaying[np].clientstr}</dd>
    </dl>
    </li>
  {/section}
</ul>

<h3>What's hot? Recently gobbled.</h3>

<ul id="recenttracks" class="listcloud">
  {section name=recent loop=$recenttracks}
   <li>
    <dl>
      <dt><a href="artist.php?artist={$recenttracks[recent].artist|stripslashes|urlencode}">
        {$recenttracks[recent].artist|stripslashes|htmlspecialchars}</a></dt>
      <dd>{$recenttracks[recent].track|stripslashes|htmlspecialchars}</dd>
    </dl>
    </li>
  {/section}
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
