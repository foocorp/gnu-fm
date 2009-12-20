<ul>
	<li><a href="{$base_url}/listen/">{t}Listen now!{/t}</a></li>
{if ($logged_in)}
	<li id="login"><a href="{$base_url}/login.php?action=logout">{t}Logout{/t}</a></li>
{else}
	<li id="login"><a href="{$base_url}/login.php">{t}Log in{/t}</a></li>
	<li id="login"><a href="{$base_url}/register.php">{t}Sign up now{/t}</a></li>
{/if}
</ul>
