{*	------------------
	tracklist.tpl
	------------------
	Dynamic smarty template intended to be used on any page with a list of tracks.

	@param array  items           Array of tracks ((artist, track, streamable, loved, image, artistlibraryurl, tracklibraryurl, artisturl, trackurl, tagged, tag, freq) .. )
	@param string class           CSS table class (if class equals 'library' *libraryurl will be used instead of *url)
	@param bool   thead           Show table header
	@param bool   fstream         Show streamable field, used by $i.streamable
	@param bool   flove           Show loved field, used by $i.loved
	@param bool   fartist         Show artist in name field, used by $i.artist
	@param bool   fimage          Show image field, used by $i.image
	@param bool   ftag            Show tag field, used by $i.tag
	@param bool   fbutton         Show button field, used by button if page is owned by user
	@param bool   fcount          Show count field, used by $i.freq
	@param bool   ftime           Show time field, used by $i.timehuman
	@param string url_sort_name   URL string to toggle sort order by name
	@param string url_sort_count  URL string to toggle sort order by count
	@param string type            Type of list, 'tagged' (used to show correct button)
*}
<table class="{$class} tracklist">
{if $thead}
	<thead>
		{if $fstream}
		<th class="icon"></th>
		{/if}
		{if $fimage}
		<th></th>
		{/if}
		<th><a href="{$url_sort_name}">Track</a></th>
		{if $flove}
		<th class="icon"></th>
		{/if}
		{if $ftag}
		<th class="icon"></th>
		{/if}
		{if $fbutton}
		<th></th>
		{/if}
		{if $fcount}
		<th class="count"><a href="{$url_sort_count}">Plays</a></th>
		{/if}
		{if $ftime}
		<th class="time">Time</th>
		{/if}
	</thead>
{/if}
	{foreach $items as $i}
	<tr>
		{if $fstream}
		{if $i.streamable}<td class="icon" title="Track is streamable"><i class="icon-music"></i></td>{else}<td class="icon"></td>{/if}
		{/if}
		{if $fimage}
			<td class="image"><img src="{$i.image}" /></td>
		{/if}
		<td class="name">
			{if $i.tracklibraryurl}
				<a href="{$i.tracklibraryurl}">{$i.track}</a>
			{else}
				<a href="{$i.trackurl}">{$i.track}</a>
			{/if}
			{if $fartist} by
				{if $i.artistlibraryurl}
					<a href="{$i.artistlibraryurl}">{$i.artist}</a>
				{else}
					<a href="{$i.artisturl}">{$i.artist}</a>
				{/if}
			{/if}
		</td>
		{if $flove}
		{if $i.loved}<td class="icon" title="{$page->user->name} loves this track"><i class="icon-heart"></i></td>{else}<td class="icon"></td>{/if}
		{/if}
		{if $ftag}
		{if $i.tagged}<td class="icon" title="{$page->user->name} has tagged this track"><i class="icon-tag"></i></td>{else}<td class="icon"></td>{/if}
		{/if}
		{if $fbutton}
		<td class="buttons">
			{if $page->ownedbyme}
				{if $type == 'scrobble'}
					<form method='post' action=''><input type=hidden name='artist' value="{$i.artist}" /><input type=hidden name='track' value="{$i.track}" /><input type=hidden name='timestamp' value="{$i.time}" /><button name='removescrobble' type='submit'>remove</button></form>
				{elseif $type == 'loved'}
					<form method='post' action=''><input type=hidden name='artist' value="{$i.artist}" /><input type=hidden name='track' value="{$i.track}" /><button name='unlove' type='submit'>remove</button></form>
				{elseif $type == 'banned'}
					<form method='post' action=''><input type=hidden name='artist' value="{$i.artist}" /><input type=hidden name='track' value="{$i.track}" /><button name='unban' type='submit'>remove</button></form>
				{elseif $type == 'tagged'}
					<form method='post' action=''><input type=hidden name='removeartist' value="{$i.artist}" /><input type=hidden name='removetrack' value="{$i.track}" /><input type=hidden name='removetag' value="{$i.tag}" /><button name='trackremovetag' type='submit'>remove</button></form>
				{/if}
			{/if}
		</td>
		{/if}
		{if $fcount}
		<td class="count"><span>{$i.freq}</span></td>
		{/if}
		{if $ftime}
		<td class="time">{$i.timehuman}</td>
		{/if}
	</tr>
	{/foreach}
</table>
