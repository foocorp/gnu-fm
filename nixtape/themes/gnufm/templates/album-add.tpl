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
			<div><h3><label for='name'>{t}Album Name{/t}</h3>
				<div class='formHelp'>{t}The name of the album{/t}</div>
				<input type='text' name='name' id='name' value='{$name|escape:'htmlall'}' />
			</div>
			<div><h3><label for='image'>{t}Cover Image{/t}</h3>
				<div class='formHelp'>{t}Address linking to the album's cover image{/t}</div>
				<input type='text' name='image' id='image' value='{$image|escape:'htmlall'}' />
			</div>
			<br />
			<p><input type='submit' name='submit' value='{t}Create Album{/t}' /></p>
		</form>
	</div>
</div>

{include file='footer.tpl'}
