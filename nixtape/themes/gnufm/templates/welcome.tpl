{include file='header.tpl'}
{if ($logged_in)}

<h2 id="txt-this-is-your-dashboard">This is your dashboard.</h2>

<ul>
<li>Have you configured your player to report your music listening habits?</li>
<li>Have you <a href="/listen/">tried our funky in-browser player</a>?</li>
</ul>

<h2><a href="{$this_user->getURL()}">Go to your profile</a> or <a href="{$this_user->getURL()}/stats">view your listening statistics</a>.</h2>

{else}

       <ul id="buttons">
	 <li><a href="/register.php">Sign up now</a></li>
	 </ul>

{/if}

{include file='footer.tpl'}
