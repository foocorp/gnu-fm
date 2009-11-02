{include file='header.tpl'}
{if ($logged_in)}

<h2><img src="http://s.libre.fm/librefm/img/dashboard.png" alt="Your dashboard." /></h2>

<ul>
<li>Have you configured your player to report your music listening habits?</li>
<li>Have you tried our funky in-browser player?</li>
</ul>

<h2><a href="{$this_user->getURL()}">Go to your profile</a> or <a href="{$this_user->getURL()}/stats">view your listening statistics</a>.</h2>

{else}

<h2><img src="http://s.libre.fm/librefm/img/more-fun-logged-in.png" alt="A lot more fun if you're logged in." /></h2>

<form action="{$base_url}/login.php" method="post">
{include file='login-form.tpl'}
</form>

<p>No account? No problem, <a href="/register.php">sign up now</a>.</p>

{/if}

{include file='footer.tpl'}
