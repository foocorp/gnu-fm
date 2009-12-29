{include file='header.tpl'}
{if ($logged_in)}

<h2 id="txt-this-is-your-dashboard">This is your dashboard.</h2>

<ul>
<li>Have you <a href="http://bugs.libre.fm/wiki/Client_Support">configured your player</a> to report your music listening habits?</li>
<li>Have you <a href="/listen/">tried our funky in-browser player</a>?</li>
</ul>

<h2><a href="{$this_user->getURL()}">Go to your profile</a> or <a href="{$this_user->getURL()}/stats">view your listening statistics</a>.</h2>

{else}

       <h2><img src="http://s.libre.fm/librefm/img/better-deal.png" alt="A better deal for artists and fans" /></h2>

       <h3 style="text-align: center; color #aaa;">Libre.fm allows you to share your listening habits and discover new music.</h3>

       <ul id="benefits">
	 <li>100% indie artists.<ul><li>Libre.fm actively supports the creation of music by independent artists.</li></ul></li>

	 <li>You own your own listening data.<ul><li>Everything you put into Libre.fm is yours, not ours. Take it away and do cool things!</li></ul></li>
	 <li>Legally download and share any track.<ul><li>Every song on Libre.fm is made by musicians who <b>want</b> you to share their music.</li></ul></li>
	 <li>Your privacy, taken care of.<ul><li>Our <a href="more.html#privacy">privacy policy</a> is awesome. We don't even log your IP address! Privacy is important.</li></ul></li>

       </ul>

       <ul id="buttons">
	 <li><a href="http://alpha.libre.fm/register.php"><img src="http://s.libre.fm/librefm/img/signup-button.png" alt="Sign up now" /></a></li>
	 </ul>

       <h4>That's not all! The code that powers Libre.fm is <a href="http://www.gnu.org/philosophy/free-sw.html">free software</a> &mdash; <a href="http://bzr.savannah.gnu.org/lh/librefm/">take it</a>, run your own site and <a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">join the development community!</a></h4>

{/if}

{include file='footer.tpl'}
