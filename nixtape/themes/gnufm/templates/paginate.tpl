<div class="pagination pagination-centered pagination-small">
	<ul>
		<li {if $page->page_number <= 1}class="disabled"{/if}><a href="{$page->urls.page_prev}">&larr;</a></li>
		<li><span>{$page->page_number}</span></li>
		<li><a href="{$page->urls.page_next}">&rarr;</a></li>
	</ul>
</div>
