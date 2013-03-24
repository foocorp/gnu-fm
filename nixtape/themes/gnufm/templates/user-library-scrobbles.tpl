{include file='header.tpl' subheader='user-header.tpl'}

<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />

{include file='paginate.tpl'}
{if $page->scrobbles}
	{include file='tracklist.tpl' class=#librarytable# items=$page->scrobbles thead=true fartist=true ftag=true ftime=true fbutton=true flove=true fstream=true type='scrobble'}
{/if}
{include file='paginate.tpl'}

{include file='footer.tpl'}
