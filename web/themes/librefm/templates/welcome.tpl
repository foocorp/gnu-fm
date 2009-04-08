{include file='header.tpl'}

<h2>Welcome</h2>

<p><strong>libre.fm</strong> is a free network service that will allow users to share their
musical tastes with other people.</p>

<h3>What's hot? Recently gobbled.</h3>

<ul id="recenttracks" class="listcloud">
  {section name=recent loop=$recenttracks}
  <li>
    <dl>
      <dt><a href="artist.php?artist={$recenttracks[recent].artist|stripslashes|urlencode}">
        {$recenttracks[recent].artist|stripslashes}</a></dt>
      <dd>{$recenttracks[recent].track|stripslashes}</dd>
    </li>
  {/section}
</ul>

{include file='footer.tpl'}
