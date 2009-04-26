{include file='header.tpl'}

<h2>Welcome</h2>
<p><strong><span class='vcard fn org'>libre.fm</span></strong> is a free network service that will allow users to share their
musical tastes with other people.</p>

<h3>Now playing</h3>

<dl class='now-playing'>
  {section name=np loop=$nowplaying}
    <dt class='artist-name'>
        <a href='{$nowplaying[np].artisturl}'>{$nowplaying[np].artist|stripslashes|htmlspecialchars}</a>
    </dt>
    <dd class='track-name'>
        {if $nowplaying[np].mbid <> ''}
        <a href='http://musicbrainz.org/track/{$nowplaying[np].mbid}.html'>
        {else}
        <a href="{$nowplaying[np].trackurl}">
        {/if}
        {$nowplaying[np].track|stripslashes|htmlspecialchars}
        </a>
    </dd>
    <dd class='username'><a href='{$nowplaying[np].userurl}'>{$nowplaying[np].username|stripslashes|htmlspecialchars}</a></dd>
    <dd>using <span class='gobbler'>{$nowplaying[np].clientstr}</span></dd>
  {/section}
</dl>

<h3>What's hot? Recently played.</h3>

<dl class='recent-tracks'>
  {section name=recent loop=$recenttracks}
      <dd class='artist-name'><a href='{$recenttracks[recent].artisturl}'>
        {$recenttracks[recent].artist|stripslashes|htmlspecialchars}</a>:
      <span class='track-name'><a href="{$recenttracks[recent].trackurl}">{$recenttracks[recent].track|stripslashes|htmlspecialchars}</a></span> &mdash;
      <span class='username'><a href='{$recenttracks[recent].userurl}'>{$recenttracks[recent].username|stripslashes|htmlspecialchars}</a></span></dd>
{if $recenttracks[recent].license > 0}
    <dd><img src="{$base_url}/themes/librefm/images/square.png" alt="[libre]" /></a>
{/if}
  {/section}
    </dl>
<div class='cleaner'>&nbsp;</div>
{include file='footer.tpl'}
