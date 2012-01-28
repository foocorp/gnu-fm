{include file='header.tpl'}

{if isset($errors)}
<div id="errors">
{section loop=$errors name=error}
	<p>{$errors[error]}</p>
{/section}
</div>
{/if}

<div about="{$id}" typeof="mo:Record" class="haudio">

	<div class="vcard">
		<form action='' method='post'>
        	        <div>
				<h3><label for='name'>Track Name</h3>
                	        <div class='formHelp'>The name of the track</div>
				<input type='text' name='name' id='name' {if $edit}disabled{/if} value='{$name|escape:'htmlall'}' />
	                </div>
			<div>
				<h3><label for='url'>Streaming URL</h3>
				<div class='formHelp'>A link to an Ogg Vorbis file hosted on <a href='http://archive.org'>archive.org</a></div>
				<input type='text' name='streaming_url' id='streaming_url' value='{$streaming_url|escape:'htmlall'}' />
			</div>
			<br />
			<p><input type='submit' name='submit' value='{if $edit}Edit{else}Create{/if} Track' /></p>
		</form>
	</div>
</div>

{include file='footer.tpl'}
