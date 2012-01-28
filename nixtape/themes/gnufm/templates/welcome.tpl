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

	<center><p>{t site=$site_name}%1 allows you to share your listening habits and discover new music.{/t}</p></center>
	<ul id="buttons">
		<li><a href="{$base_url}/register.php"><img src="{$img_url}/signup-button.png" alt="{t}Sign up now{/t}" /></a></li>
	</ul>

	<h4>{t site=$site_name escape=no}That's not all! The code that powers %1 is <a href="http://www.gnu.org/philosophy/free-sw.html">free software</a> &mdash; <a href="http://gitorious.org/foocorp/gnu-fm">take it</a>, run your own site and <a href="http://lists.nongnu.org/mailman/listinfo/librefm-discuss">join the development community!{/t}</a></h4>

{/if}
	<br /><br />
	<center>
	<div>Are you an artist? <a href="{$base_url}/artist-signup.php">Sign up now</a> to start sharing your music with our listeners!</div>
	</center>

{include file='footer.tpl'}
