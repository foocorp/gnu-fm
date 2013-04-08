{include file='header.tpl' subheader='user-header.tpl'}

<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />

{include file='paginate.tpl'}
{if $page->banned_tracks}
	{include file='tracklist.tpl' class=#librarytable# items=$page->banned_tracks thead=true flove=true fartist=true ftime=true fbutton=true type='banned'}
{/if}
{include file='paginate.tpl'}

{include file='footer.tpl'}
