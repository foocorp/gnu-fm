<ul>
{if ($logged_in)}
	<li><a href="{$this_user->getURL()}">{$this_user->name}</a></li>
	<li><a href="{$base_url}/listen.php">{t}Listen{/t}</a></li>
	<li><a href="https://savannah.nongnu.org/bugs/?group=librefm">{t}Bugs{/t}</a></li>
	<li><a href="http://ideas.libre.fm/index.php/Using_turtle">{t}Help{/t}</a></li>
	{if $this_user->userlevel > 0}
	<li><a href="/admin.php">{t}Admin{/t}</a></li>
	{/if}
	<li><a href="{$base_url}/login.php?action=logout">{t}Logout{/t}</a></li>
{else}
        <li><a href="{$base_url}/login.php?return={$this_page|urlencode|htmlentities}">{t}Login{/t}</a></li>
{/if}
</ul>
