    <ul>
{if ($logged_in)}
        <li><a href="{$u_user->getURL()}">{$u_user->name}</a></li>
{/if}

{if ($logged_in)}
    {if $u_user->userlevel > 0}
        <li><a href="/admin.php">Admin panel</a></li>
    {/if}
{else}
        <li><a href="{$base_url}/login.php">Login</a></li>
        <li><a href="{$base_url}/request.php">Request invitation</a></li>
{/if}
        <li>Explore:</li>
        <li><a href="explore.php?mode=artists">Artists</a></li>
  </ul>
