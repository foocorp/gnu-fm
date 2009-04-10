{include file='header.tpl'}

<h2>Welcome</h2>
<p><strong><span class='vcard fn org'>libre.fm</span></strong> is a free network service that will allow users to share their
musical tastes with other people.</p>

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
