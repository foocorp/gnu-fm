{include file='header.tpl'}

{if isset($errors)}
<div id="errors">
{section loop=$errors name=error}
        <p>{$errors[error]}</p>
{/section}
</div>
{/if}

<div about="{$id}" typeof="mo:MusicArtist">

	<div class="vcard">
		<form action='' method='post'>
		<div><h3><label for='homepage'>Home Page</h3>
			<div class='formHelp'>A link to the {$name|escape:'htmlall'}'s website.</div>
			<input type='text' name='homepage' id='homepage' value='{$homepage|escape:'htmlall'}' />
		</div>
		<div><h3><label for='bio_summary'>Short Biography</h3>
			<div class='formHelp'>Provide a short summary of {$name|escape:'htmlall'}'s biography.</div>
			<textarea name='bio_summary' id='bio_summary'>{$bio_summary|escape:'html':'UTF-8'}</textarea>
		</div>
		<div><h3><label for='bio_content'>Full Biography</h3>
			<div class='formHelp'>Provide a more detailed biography of {$name|escape:'htmlall'}.</div>
			<textarea name='bio_content' id='bio_content'>{$bio_content|escape:'html':'UTF-8'}</textarea>
		</div>
		<div><h3><label for='image'>Image</h3>
			<div class='formHelp'>Provide a link to an image of {$name|escape:'htmlall'}, this should be no larger than 256x256 pixels. If you need somewhere to upload your image we recommend <a href='http://imgur.com'>Imgur</a>, <a href='http://www.flickr.com'>Flickr</a> or <a href='http://www.archive.org'>Archive.org</a>.</div>
			<input type='text' name='image' id='image' value='{$image|escape:'htmlall'}' />
		</div>
		<div><h3><label for='flattr_uid'>Flattr Username</h3>
			<div class='formHelp'>If you have a <a href='http://flattr.com'>flattr account</a> simply enter your flattr username and we'll automatically put flattr buttons on your artist and track pages. This way your fans will be able to show their appreciation with the occasional tip.</div>
			<input type='text' name='flattr_uid' id='flattr_uid' value='{$flattr_uid|escape:'htmlall'}' />
		</div>
		<br />
		<p><input type='submit' name='submit' value='Update' /></p>
	</div>

	<ul>
		{section name=i loop=$albums}
			{if $albums[i]->name}
			<li about="{$albums[i]->id}" property="dc:title" content="{$albums[i]->name|escape:'html':'UTF-8'}" typeof="mo:Record" class="haudio">
					<a rel="foaf:page" href="{$albums[i]->getURL()}">{$albums[i]->name|escape:'html':'UTF-8'}</a>
			</li>
			{/if}
		{/section}
	</ul>

	<br />

</div>

{include file='footer.tpl'}

