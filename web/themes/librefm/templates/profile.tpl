{include file='header.tpl'}

<h2>{$user}'{if $user|substr:-1 != 's'}s{/if} profile</h2>
<dl class='user vcard'>
    <dt class='fn'>
        <span class='family-name'>{$fullname}</span>
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
<h3>Latest {$scrobbles|@count} Gobbles:</h3>
{section name=i loop=$scrobbles}
    {if $smarty.capture.artist_last <> $scrobbles[i].artist}
        {if $scrobbles[i] != $scrobbles[0]} 
        </dl>
        {/if}
        <dl class='gobbles'>
            <dt class='artist'>
                <a href="artist.php?artist={$scrobbles[i].artist|stripslashes|urlencode|htmlspecialchars}">{$scrobbles[i].artist}</a>
            </dt>
    {/if}
      <dd class='gobble'><span class='track-name'>{$scrobbles[i].track|stripslashes}</span><small>{$scrobbles[i].timehuman}</small></dd>
      {capture name=artist_last}{$scrobbles[i].artist}{/capture}
    {if $smarty.capture.artist_last <> $scrobbles[i].artist}

    {/if}
{/section}
    </dl>


{include file='footer.tpl'}
