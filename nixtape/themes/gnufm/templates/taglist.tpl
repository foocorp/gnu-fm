{*	------------------
	taglist.tpl
	------------------
	Dynamic smarty template intended to be used on any page with a list of tags.

	@param array  items           Array of tracks ((tag, image, taglibraryurl, tagurl, freq) .. )
	@param string class           CSS table class (if class equals 'library' *libraryurl will be used instead of *url)
	@param bool   thead           Show table header
	@param bool   fimage          Show image field, used by $i.image
	@param bool   fcount          Show count field, used by $i.freq
	@param string url_sort_name   URL string to toggle sort order by name
	@param string url_sort_count  URL string to toggle sort order by count
*}
<table class="{$class} taglist">
	{if $thead}
	<thead>
		{if $fimage}
		<th></th>
		{/if}
		<th><a href="{$url_sort_name}">Tag</a></th>
		{if $fcount}
		<th class="count"><a href="{$url_sort_count}">Count</a></th>
		{/if}
	</thead>
	{/if}
	{foreach $items as $i}
	<tr>
		{if $fimage}
		<td class="image"><img src="{$i.image}" /></td>
		{/if}
		<td class="name">
		{if $i.taglibraryurl}
			<a href="{$i.taglibraryurl}">{$i.tag}</a></td>
		{else}
			<a href="{$i.tagurl}">{$i.tag}</a></td>
		{/if}
		{if $fcount}
		<td class="count"><span>{$i.freq}</span></td>
		{/if}
	</tr>
	{/foreach}
</table>
