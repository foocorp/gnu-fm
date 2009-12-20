{include file='header.tpl'}
{if ($logged_in)}

<h2 id="txt-this-is-your-dashboard">This is your dashboard.</h2>

<ul>
<li>Have you <a href="http://bugs.libre.fm/wiki/Client_Support">configured your player</a> to report your music listening habits?</li>
<li>Have you <a href="/listen/">tried our funky in-browser player</a>?</li>
</ul>

<h2><a href="{$this_user->getURL()}">Go to your profile</a> or <a href="{$this_user->getURL()}/stats">view your listening statistics</a>.</h2>

{else}

<h2 id="txt-a-lot-more-fun-if-youre-logged-in">A lot more fun if you're logged in.</h2>

<form action="{$base_url}/login.php" method="post">
{include file='login-form.tpl'}
</form>

<p>No account? No problem, <a href="/register.php">sign up now</a>.</p>

{/if}

{include file='footer.tpl'}
