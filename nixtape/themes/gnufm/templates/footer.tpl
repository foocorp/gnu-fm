</section>
</article>
<aside>
				{include file='search-box.tpl'}
			{foreach from=$sideblocks item=block}
				<div class='sideblock-top'></div>
				{include file=$block}
				<div class='sideblock-bottom'></div>
			{foreachelse}
				<div class='sideblock-top'></div>
				{include file='sidebar-tips.tpl'}
				<div class='sideblock-bottom'></div>
			{/foreach}
			<div class='sideblock-top'></div>
			{include file='sidebar-ads.tpl'}
			<div class='sideblock-bottom'></div>
</aside>
</div>
</div>
<div id="footer-container">
     <footer class="wrapper">
		{include file='language-selector.tpl'}<br />

	<p>{t site=$site_name escape=no}%1 is powered by the <a href="http://www.gnu.org/software/fm">GNU FM</a> <a href="http://www.gnu.org/philosophy/free-sw.html">free software</a> system &mdash; <a href="http://gitorious.org/foocorp/gnu-fm">take it</a>, run your own site and <a href="http://lists.nongnu.org/mailman/listinfo/librefm-discuss">join the development community!{/t}</a>.</p>
     </footer>
</div>
</body>
</html>