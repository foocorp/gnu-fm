<ul>
{if ($logged_in)}
        <li><a href="{$this_user->getURL()}">{t}Profile{/t}</a></li>
	<li><a href="{$base_url}/listen.php">{t}Listen{/t}</a></li>
        <li><a href="{$base_url}">{t}Popular{/t}</a></li>
	<li><a href="{$base_url}/login.php?action=logout">{t}Logout{/t}</a></li>
{else}
	<li><a href="{$base_url}/login.php">{t}Log in{/t}</a></li>
	<li><a href="{$base_url}/listen.php">{t}Listen{/t}</a></li>
	<li><a href="{$base_url}/popular-page.php">{t}Popular{/t}</a></li>
	{if !($registration_disabled)}
	<li><a href="{$base_url}/register.php">{t}Sign up{/t}</a></li>
	{/if}
{/if}
</ul>
