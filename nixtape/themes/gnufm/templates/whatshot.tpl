<h3>What's hot? Recently played.</h3>

<dl class='recent-tracks'>
  {section name=recent loop=$recenttracks}
{if $recenttracks[recent].license > 0}
      <dt class='artist-name libre'><a title="Libre artist" href='{$recenttracks[recent].artisturl}'>
        {$recenttracks[recent].artist|stripslashes|htmlspecialchars}</a>:
{else}
      <dt class='artist-name'><a href='{$recenttracks[recent].artisturl}'>
        {$recenttracks[recent].artist|stripslashes|htmlspecialchars}</a>:
{/if}
      <span class='track-name'><a href="{$recenttracks[recent].trackurl}">{$recenttracks[recent].track|stripslashes|htmlspecialchars}</a></span> &mdash;
      <span class='username'><a href='{$recenttracks[recent].userurl}'>{$recenttracks[recent].username|stripslashes|htmlspecialchars}</a></span></dd>
  {/section}
    </dl>
<div class='cleaner'>&nbsp;</div>