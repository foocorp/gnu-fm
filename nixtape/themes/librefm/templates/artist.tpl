{include file='header.tpl'}

<div about="{$id}" typeof="mo:MusicArtist">

	<div class="vcard">
		<h2 class="fn org" property="foaf:name" rel="foaf:page" rev="foaf:primaryTopic" resource="">{$name|escape:'htmlall'}</h2>
		{if $bio_summary}
		<div class="note" id="bio" property="bio:olb" datatype="">{$bio_summary}</div>
		{/if}
	</div>

	<ul>
		{section name=i loop=$albums}
{if $albums[i]->name}
		<li about="{$albums[i]->id}" property="dc:title" content="{$albums[i]->name|escape:'html':'UTF-8'}" typeof="mo:Record" class="haudio">
					<a rel="foaf:page" href="{$albums[i]->getURL()}">{$albums[i]->name|escape:'html':'UTF-8'}</a>
		</li>{/if}
		{/section}
	</ul>

</div>

{include file='footer.tpl'}

