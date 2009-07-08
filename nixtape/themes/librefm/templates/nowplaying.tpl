<h3>Now playing</h3>

<dl class='now-playing'>
  {section name=np loop=$nowplaying}
{if $nowplaying[np].license > 0}
    <dt class='artist-name libre'>
{else}
    <dt class='artist-name'>
{/if}
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
