{include file='header.tpl' subheader='user-header.tpl'}

<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />

{include file='paginate.tpl'}

{if $page->artists}
	{include file='artistlist.tpl' class=#librarytable# items=$page->artists thead=true fstream=true fimage=true fcount=true}
{/if}

{include file='paginate.tpl'}

{if $page->streamable}
	<a href="{$page->urls['streamable']}">All artists</a>
{else}
	<a href="{$page->urls['streamable']}">Streamable artists</a>
{/if}

{include file='footer.tpl' nosidebar=true}
