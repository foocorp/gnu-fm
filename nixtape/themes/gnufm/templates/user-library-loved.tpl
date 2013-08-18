{include file='header.tpl' subheader='user-header.tpl'}

<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />

{include file='paginate.tpl'}
{if $page->loved_tracks}
	{include file='tracklist.tpl' class=#librarytable# items=$page->loved_tracks thead=true fstream=true flove=true fartist=true ftime=true fbutton=true type='loved'}
{/if}
{include file='paginate.tpl'}

{include file='footer.tpl'}
