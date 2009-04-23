    <ul>
{if ($logged_in)}
        <li><a href="{$u_user->getURL()}">{$u_user->name}</a></li>
{else}
	<li><a href="{$base_url}/register.php">Register</a></li>
{/if}

{if ($logged_in)}
    {if $u_user->userlevel > 0}
        <li><a href="/admin.php">admin</a></li>
    {/if}
	<li><a href="{$base_url}/login.php?action=logout">Logout</a></li>
{else}
        <li><a href="{$base_url}/login.php?return={$this_page|urlencode|htmlentities}">Login</a></li>
{/if}
	<li><a href="https://savannah.nongnu.org/bugs/?group=librefm">Bugs</a></li>
	<li><a href="http://ideas.libre.fm/index.php/Using_turtle">Help</a></li>
  </ul>
