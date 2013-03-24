{include file='header.tpl' subheader='user-header.tpl'}
<h4 class="inline">Library</h4> {include file='submenu.tpl' submenu=$page->menu}
<br />
<br />
<div>
	<img style="float:left;width:32px;height:32px;margin-right:10px;" src="{$page->artist_image}" />
	<div>
		<div style="border-bottom:1px solid rgb(221,221,221);width:calc(100% - 50px);display:inline-block;">
			<a href="{$page->artist_library_url}">{$page->artist->name}</a> : <b>{$page->track->name}</b>
		</div>
		<a href="{$page->track_url}">Go to track page</a>
	</div>
</div>
<br />


<h4>Personalized track info here</h4>

{include file='footer.tpl'}
