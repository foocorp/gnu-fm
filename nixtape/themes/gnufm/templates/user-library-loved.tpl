{include file='header.tpl' subheader='user-header.tpl'}
<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />

{if count($page->loved_tracks) == 50}
<div class="center">
<a class="pull-left" {if $page->page <= 1}style="visibility:hidden;"{/if} href="{$page->pageurls['page_prev']}">prev</a> <span>page {$page->page}</span><a class="pull-right" href="{$page->pageurls['page_next']}">next</a>
</div>
{/if}

{if $page->loved_tracks}
	{include file='tracklist.tpl' class=#librarytable# items=$page->loved_tracks fstream=true flove=true fartist=true ftime=true fbutton=true type='loved'}
{/if}
{include file='footer.tpl'}
