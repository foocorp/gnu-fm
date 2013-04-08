{*	------------------
	albumlist.tpl
	------------------
	Dynamic smarty template intended to be used on any page with a list of albums.

	@param array  items           Array of albums ((artist, album, streamable, image, artistlibraryurl, albumlibraryurl, artisturl, albumurl, tagged, tag, freq) .. )
	@param string class           Additional CSS table classes
	@param bool   thead           Show table header
	@param bool   fstream         Show streamable field, used by $i.streamable
	@param bool   fartist         Show artist in name field, used by $i.artist
	@param bool   fimage          Show image field, used by $i.image
	@param bool   fbutton         Show button field, used by button if page is owned by user
	@param bool   fcount          Show count field, used by $i.freq)
	@param string url_sort_name   URL string to toggle sort order by name
	@param string url_sort_count  URL string to toggle sort order by count
	@param string type            Type of list, 'tagged' (used to show correct button)
*}
<table class="{$class} albumlist">
{if $thead}
	<thead><tr>
		{if $fstream}
		<th class="icon"></th>
		{/if}
		{if $fimage}
		<th></th>
		{/if}
		<th class="title">Title</th>
		{if $fbutton}
		<th class="buttons"></th>
		{/if}
		{if $fcount}
		<th class="count">Plays</th>
		{/if}
	</thead>
{/if}
	{foreach from=$items item=i}
	<tr>
		{if $fstream}
		{if $i.streamable}<td class="icon" title="Album has streamable tracks"><i class="icon-music"></i></td>{else}<td class="icon"></td>{/if}
		{/if}
		{if $fimage}
			<td class="image"><img src="{$i.image}" /></td>
		{/if}
		<td class="name">
			{if $i.albumlibraryurl}
				<a href="{$i.albumlibraryurl}">{$i.album}</a>
			{else}
				<a href="{$i.albumurl}">{$i.album}</a>
			{/if}
			{if $fartist} by
				{if $i.artistlibraryurl}
					<a href="{$i.artistlibraryurl}">{$i.artist}</a>
				{else}
					<a href="{$i.artisturl}">{$i.artist}</a>
				{/if}
			{/if}
		</td>
		{if $fbutton}
			<td class="buttons">
				{if $page->ownedbyme}
					{if $type == 'tagged'}
					<form method='post' action=''><input type=hidden name='removeartist' value="{$i.artist}" /><input type=hidden name='removealbum' value="{$i.album}" /><input type=hidden name='removetag' value="{$i.tag}" /><button name='albumremovetag' type='submit'>remove</button></form>
					{/if}
				{/if}
			</td>
		{/if}
		{if $fcount}
		<td class="count"><span>{$i.freq}</span></td>
		{/if}
		{if $ftime}
		<td class="time"><span>{$i.timehuman}</span></td>
		{/if}
	</tr>
	{/foreach}
</table>
