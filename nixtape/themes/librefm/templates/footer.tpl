{if ($sidebar)}	</div>
</div>
	</div>
	<div class="yui-b">

<!-- sidebar -->

	{include file='adbard.tpl'}     

	{include file=$sidebartemplate}

</div></div>

{else}
</div></div>
{/if}

	<div id="ft" role="navigation" style="clear:both;">

		{include file='language-selector.tpl'}

		<ul>
			<li class="copy">&copy; 2009 <a href="http://libre.fm/">Libre.fm</a> Project</li>
			<li><a href="http://libre.fm/contributors/">{t}Contributors{/t}</a></li>
			<li><a href="http://libre.fm/licensing/">{t}Licensing information{/t}</a></li>
			<li><a href="http://libre.fm/developers/">{t}Developers{/t}</a></li>
			<li><a href="http://libre.fm/api/">API</a></li>
			<li><a href="http://libre.fm/download/">{t}Download{/t}</a></li>
			<li><a href="http://libre.fm/translations/">{t}Help translate Libre.fm{/t}</a></li>
		</ul>

		<ul>
			<li>{t escape=no}A <a href="http://foocorp.org/">FooCorp</a> thing.{/t}</li>
			<li><a href="http://autonomo.us/">autonomo.us</a></li>
			<li><a href="http://nixtape.org/">{t}Powered by Nixtape{/t}</a></li>
		</ul>

	</div>
</div>
</body>
</html>
