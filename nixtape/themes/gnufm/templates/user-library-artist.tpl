{include file='header.tpl' subheader='user-header.tpl'}

<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />
<div>
	<img style="float:left;width:32px;height:32px;margin-right:10px;" src="{$page->artist_image}" />
	<div>
		<div style="border-bottom:1px solid rgb(221,221,221);width:calc(100% - 50px);display:inline-block;">
			<a href="{$page->section_url}">Artists</a> : <h5 style="display:inline;">{$page->artist->name}</h5> {if $page->artist->homepage}<a href="{$page->artist->homepage}">website</a>{/if}
		</div>
		<a href="{$page->artist_url}">Go to artist page</a>
	</div>
</div>

<br />
{if $page->albums}
	{include file='albumlist.tpl' class=#librarytable# items=$page->albums thead=true fimage=true fcount=true fstream=true}
{/if}

{if $page->tracks}
	{include file='tracklist.tpl' class=#librarytable# items=$page->tracks thead=true fstream=true flove=true ftag=true fcount=true}
{/if}

{include file='footer.tpl'}
