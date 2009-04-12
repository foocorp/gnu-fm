{include file='header.tpl'}

<h2>{$user}'{if $user|substr:-1 != 's'}s{/if} profile</h2>
<dl class='user vcard'>
    <dt class='fn'>
        <span class='family-name'>{$fullname|utf8_encode}</span>
        (<span class='nickname'>{$user}</span>)
    </dt>
    <dd class='avatar'>
        <!-- Avatar placeholder  -->
        <img src='{$avatar}' class='photo' alt="avatar" />
    </dd>
    <dd class='org'>
        <a href='{$homepage}' rel='bookmark' class='url'>{$homepage}</a>
    </dd>
    <dd class='adr'>
        <span class='locality'>{$location}</span>
    </dd>
    <dd class='bio'>
        <p>{$bio}</p>
    </dd>
</dl>
{if $nowplaying|@count > 0}
<h3>Now Playing:</h3>
<dl class='now-playing'>
    {section name=i loop=$nowplaying}
    <dt class='track-name'>{$nowplaying[i].track}</dt>
    <dd>by <span class='artist-name'><a href='{$nowplaying[i].artisturl}'>{$nowplaying[i].artist}</a></span></dd>
    <dd>with <span class='gobbler'>{$nowplaying[i].clientstr}</span></dd>
    {/section}
</dl>
{/if}

<h3>Latest {$scrobbles|@count} Gobbles:</h3>
{section name=i loop=$scrobbles}
    {if $smarty.capture.artist_last <> $scrobbles[i].artist}
        {if $scrobbles[i] != $scrobbles[0]} 
        </dl>
        {/if}
        <dl class='gobbles'>
            <dt class='artist'>
                <a href="{$scrobbles[i].artisturl}">{$scrobbles[i].artist}</a>
            </dt>
    {/if}
      <dd class='gobble'><span class='track-name'>{$scrobbles[i].track|stripslashes}</span><small>{$scrobbles[i].timehuman}</small></dd>
      {capture name=artist_last}{$scrobbles[i].artist}{/capture}
{/section}
    </dl>
</div></div>
    <div class="yui-u" id="sidebar">
    <div style="padding: 10px;">
      <h3>User's favorite artists</h3>
<ul class="tagcloud">
{section name=i loop=$tagcloud}
  <li style='font-size:{$tagcloud[i].size}'><a href='/artist/{$tagcloud[i].artist|urlencode}' rel='tag'>{$tagcloud[i].artist}</a></li>
{/section}
  </ul>
{include file='footer.tpl'}
