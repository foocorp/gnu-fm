<ul>
{if ($logged_in)}
	<li><a href="{$base_url}/listen/">{t}Listen now!{/t}</a></li>
	<li><a href="{$this_user->getURL()}">Your profile</a> (<a href="/user-edit.php">edit</a>)</li>
	<li><a href="{$this_user->getURL()}/stats">Your stats</a></li>
	<li><a href="{$this_user->getURL()}/recent-tracks">Recent tracks</a></li>
	<li><a href="{$this_user->getURL()}/groups">Groups</a></li>
	<li id="login"><a href="{$base_url}/login.php?action=logout">{t}Logout{/t}</a></li>
{else}
	<li id="login"><a href="{$base_url}/login.php">{t}Log in{/t}</a></li>
	<li id="login"><a href="{$base_url}/register.php">{t}Sign up now{/t}</a></li>
{/if}
<li><a href="/chat.html" target="_blank">Live help chat</a></li>
</ul>
