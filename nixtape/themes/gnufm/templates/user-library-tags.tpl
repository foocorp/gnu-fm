{include file='header.tpl' subheader='user-header.tpl'}
<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />


{if $page->tag_name}

<div>
	<img style="float:left;width:32px;height:32px;margin-right:10px;" src="{$page->tag_image}" />
	<div>
		<div style="border-bottom:1px solid rgb(221,221,221);width:calc(100% - 50px);display:inline-block;">
			<a href="{$page->section_url}">Tags</a> : <b>{$page->tag_name}</b>
		</div>
		<a href="{$page->tag_url}">Go to tag page</a>
	</div>
</div>
<br />

	{if $page->tagged_artists}
		<p><b>Artists</b> tagged with <b>{$page->tag_name}</b></p>
		{include file='artistlist.tpl' class=#librarytable# owner=$page->user->name items=$page->tagged_artists fimage=true fstream=true fbutton=true type='tagged'}
	{/if}
	{if $page->tagged_albums}
		<p><b>Albums</b> tagged with <b>{$page->tag_name}</b></p>
		{include file='albumlist.tpl' class=#librarytable# items=$page->tagged_albums fartist=true fimage=true fstream=true fbutton=true type='tagged'}
	{/if}
	{if $page->tagged_tracks}
		<p><b>Tracks</b> tagged with <b>{$page->tag_name}</b></p>
		{include file='tracklist.tpl' class=#librarytable# items=$page->tagged_tracks fartist=true fstream=true fbutton=true type='tagged'}
	{/if}
{else}

	{if $page->tags}
		{include file='taglist.tpl' class=#librarytable# items=$page->tags fcount=true}
	{/if}
{/if}

{include file='footer.tpl'}
