{if $flattr_uid}
	<div class="flattr" style="padding-left: 2em;">
		<a class="FlattrButton" style="display:none;"
			title="{$track->artist_name|escape:'htmlall'} - {$track->name|escape:'htmlall'}"
			rev="flattr;uid:{$flattr_uid|escape:'htmlall'};category:audio;tags:music,creative commons,free,libre.fm;"
			href="{$url}">{$track->artist_name|escape:'htmlall'} is making {$track->name|escape:'htmlall'} freely available for you to listen to.</a>
	</div>
{/if}
