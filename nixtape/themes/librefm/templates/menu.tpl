<ul>
{if ($logged_in)}
	<li><a href="{$base_url}/listen/">{t}Listen now!{/t}</a></li>
	<li id="login"><a href="{$base_url}/login.php?action=logout">{t}Logout{/t}</a></li>
{/if}
</ul>
