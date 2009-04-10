<div id="menu">
    <ul id="navigation">
{if ($logged_in)}
        <li><a href="{$base_url}/profile.php?user={$u_username|urlencode}">{$u_username}</a></li>
{/if}

{if ($logged_in)}
        <li>Invites disabled</li>
    {if $u_userlevel > 0}
        <li><a href="/admin.php">Admin panel</a></li>
    {/if}
{else}
        <li><a href="{$base_url}/login.php">Login</a></li>
        <li><a href="{$base_url}/request.php">Request invitation</a></li>
{/if}
        <li>Explore:</li>
        <li><a href="explore.php?mode=artists">Artists</a></li>
  </ul>
</div>
