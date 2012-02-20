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

		<p>{t escape=no}Powered by <a href="http://gnu.org/software/fm/">GNU FM{/t}</a></p>
     </footer>
</div>
</body>
</html>