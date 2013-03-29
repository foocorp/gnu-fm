{*	------------------
	artistlist.tpl
	------------------
	Dynamic smarty template intended to be used on any page with a list of artists.

	@param array  items           Array of artists ((artist, streamable, image, artistlibraryurl, artisturl, tagged, tag, freq, time) .. )
	@param string class           Additional CSS table classes
	@param bool   thead           Show table header
	@param bool   fstream         Show streamable field, used by $i.streamable
	@param bool   fimage          Show image field, used by $i.image
	@param bool   ftag            Show tag field, used by $i.tag
	@param bool   fbutton         Show button field, used by button if list is owned by user
	@param bool   fcount          Show count field, used by $i.freq)
	@param boot   ftime           Show timestamp field, used by $i.time)
	@param string type            Type of list, 'tagged' (used to show correct button)
*}
<table class="{$class} artistlist">
{if $thead}
	<thead><tr>
		{if $fstream}
			<th></th>
		{/if}
		{if $fimage}
			<th></th>
		{/if}
			<th class="title"><a href="{$page->urls['sort_name']}">Title</a></th>
		{if $ftag}
			<th></th>
		{/if}
		{if $fbutton}
			<th></th>
		{/if}
		{if $fcount}
			<th><a href="{$page->urls['sort_count']}">Plays</a></th>
		{/if}
		{if $ftime}
			<th></th>
		{/if}
	</tr></thead>
{/if}
	{foreach from=$items item=i}
	<tr>
		{if $fstream}
		{if $i.streamable}<td class="icon" title="Artist has streamable tracks"><i class="icon-music"></i></td>{else}<td class="icon"></td>{/if}
		{/if}
		{if $fimage}
			<td class="image"><img src="{$i.image}" /></td>
		{/if}
		<td class="name">
		{if $i.artistlibraryurl}
			<a href="{$i.artistlibraryurl}">{$i.artist}</a></td>
		{else}
			<a href="{$i.artisturl}">{$i.artist}</a></td>
		{/if}
		{if $ftag}
		{if $i.tagged}<td class="icon" title="{$page->user->name} has tagged this artist"><i class="icon-tag"></i></td>{else}<td class="icon"></td>{/if}
		{/if}
		{if $fbutton}
			<td class="buttons">
			{if $page->ownedbyme}
				{if $type == 'tagged'}
					<form method='post' action=''><input type=hidden name='removeartist' value="{$i.artist}" /><input type=hidden name='removetag' value="{$i.tag}" /><button name='artistremovetag' type='submit'>remove</button></form>
				{/if}
			{/if}
			</td>
		{/if}
		{if $fcount}
		<td class="count"><span>{$i.freq}</span></td>
		{/if}
		{if $ftime}
		<td class="time">{$i.time}</td>
		{/if}
	</tr>
	{/foreach}
</table>
