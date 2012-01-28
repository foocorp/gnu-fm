{include file='header.tpl'}

<div about="{$id}" typeof="mo:MusicArtist">

	{if !empty($tagcloud)}
		<h3 style='text-align: center; clear: left;'>{t}Popular tags other people used to describe this artist{/t}</h3>
		<ul class="tagcloud">
		{section name=i loop=$tagcloud}
        		<li style='font-size:{$tagcloud[i].size}'><a href='/tag/{$tagcloud[i].tag|urlencode}' title='{t uses=$tagcloud[i].count}This tag was used %1 times{/t}' rel='tag'>{$tagcloud[i].tag}</a></li>
		{/section}
		</ul>
	{/if}

	{if !empty($mytags)}
		<h3 style='text-align: center; clear: left;'>{t}Tags you've used for this artist{/t}</h3>
		<ul class="tagcloud">
		{section name=i loop=$mytags}
        		<li>{$mytags[i].tag},</li>
		{/section}
		</ul>
	{/if}

	<br />

	<form action='' method='post'>
		<b><label for='tags'>{t}Add tags:{/t}</label></b><br />
		<br />
		<input type='text' name='tags' id='tags' /><br />
		<br />
		<input type='submit' name='tag' id='tag' value='{t}Tag{/t}' />
	</form>

	{literal}
	<script type='text/javascript'>
		$(document).ready(function(){
			$("#tags").placeholdr({placeholderText: "{/literal}{t}e.g. guitar, violin, female vocals, piano{/t}{literal}"});
		});
	</script>
	{/literal}

	<br />

</div>

{include file='footer.tpl'}

