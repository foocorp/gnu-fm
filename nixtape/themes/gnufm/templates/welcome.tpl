{include file='header.tpl'}
{if ($logged_in)}
<div id="infobox">
<p>{t escape=no}Have you <a href="http://bugs.libre.fm/wiki/clients">configured your player</a> to report your music listening habits?{/t}</p>
</div>

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

	<ul id="benefits">
		<li>{t escape=no}100&#37; indie artists.{/t}
			<ul>
				<li>{t}Libre.fm actively supports the creation of music by independent artists.{/t}</li>
			</ul>
		</li>
		<li>{t}You own your own listening data.{/t}
			<ul>
				<li>{t}Everything you put into Libre.fm is yours, not ours. Take it away and do cool things!{/t}</li>
			</ul>
		</li>
		<li>{t}Legally download and share any track.{/t}
			<ul>
				<li>{t escape=no}Every song on Libre.fm is made by musicians who <b>want</b> you to share their music.{/t}</li>
			</ul>
		</li>
		<li>{t}Your privacy, taken care of.{/t}
			<ul>
				<li>{t escape=no}Our <a href="http://libre.fm/more.html#privacy">privacy policy</a> is awesome. We don't even log your IP address! Privacy is important.{/t}</li>
			</ul>
		</li>
	</ul>
	<ul id="buttons">
		<li><a href="{$base_url}/register.php"><img src="{$img_url}/signup-button.png" alt="{t}Sign up now{/t}" /></a></li>
	</ul>

	<h4>{t escape=no}That's not all! The code that powers Libre.fm is <a href="http://www.gnu.org/philosophy/free-sw.html">free software</a> &mdash; <a href="http://gitorious.org/foocorp/gnu-fm">take it</a>, run your own site and <a href="http://lists.nongnu.org/mailman/listinfo/librefm-discuss">join the development community!{/t}</a></h4>

{/if}
	<br /><br />
	<center>
	<div>Are you an artist? <a href="{$base_url}/artist-signup.php">Sign up now</a> to start sharing your music with listeners who are passionate about free culture!</div>
	</center>

{include file='footer.tpl'}
