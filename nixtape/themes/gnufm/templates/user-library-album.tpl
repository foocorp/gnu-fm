{include file='header.tpl' subheader='user-header.tpl'}
<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />
<div>
	<img style="float:left;width:32px;height:32px;margin-right:10px;" src="{$page->album_image}" />
	<div>
		<div style="border-bottom:1px solid rgb(221,221,221);width:calc(100% - 50px);display:inline-block;">
			<a href="{$page->artist_library_url}">{$page->artist->name}</a> : <b>{$page->album->name}</b>
		</div>
		<a href="{$page->album_url}">Go to album page</a>
	</div>
</div>
<br />
{if $page->tracks}
	<h4>Tracks on this album</h4>
	{include file='tracklist.tpl' class=#librarytable# items=$page->tracks fstream=true flove=true ftag=true fcount=true}
{/if}

{include file='footer.tpl'}
