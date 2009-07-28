<ul>
{if ($logged_in)}
	<li><a href="{$this_user->getURL()}">{$this_user->name}</a></li>
	<li><a href="{$base_url}/listen/">{t}Listen{/t}</a></li>
	<li id="login"><a href="{$base_url}/login.php?action=logout">{t}Logout{/t}</a></li>
{else}
<form action="{$base_url}/login.php" method="post">
{include file='login-form.tpl'}
</form>
{/if}
</ul>
