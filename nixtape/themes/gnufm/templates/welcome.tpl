{include file='header.tpl'}
{if ($logged_in)}
<div id="welcome-box">
<br />
<p>{t escape=no userurl=$this_user->getURL() statsurl=$this_user->getURL('stats')}<a href="%1">Go to your profile</a> or <a href="%2">view your listening statistics</a>.{/t}</p>

</div>

{if isset($tagcloud)}
<div id="tag-cloud-box">

    {include file='popular.tpl'}

</div>
{/if}

{else}

	{if !($registration_disabled)}
	<p class="c">{t site=$site_name}%1 allows you to share your listening habits and discover new music.{/t}</p>
	<ul id="buttons sign-up">
		<li><a href="{$base_url}/register.php">{t}Sign up now{/t}</a></li>
	</ul>
	{/if}

{/if}
	{if !($registration_disabled)}
	<p class="c artist-signup">Are you an artist? <a href="{$base_url}/artist-signup.php">Sign up now</a> to start sharing your music with our listeners!</p>
	{/if}

{include file='footer.tpl'}
