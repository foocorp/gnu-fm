{include file='header.tpl'}

<div about="{$id}" typeof="mo:MusicArtist">

	<div class="vcard">
		<h2 class="fn org" property="foaf:name" rel="foaf:page" rev="foaf:primaryTopic" resource="">{$name}</h2>
		{if $bio_summary}
		<div class="note" id="bio" property="bio:olb" datatype="">{$bio_summary}</div>
		{/if}
	</div>

	<ul id="albums" rel="foaf:made" rev="foaf:maker">
		{section name=i loop=$albums}
		<li about="{$albums[i]->id}" property="dc:title" content="{$albums[i]->name|escape:'html':'UTF-8'}" typeof="mo:Record" class="haudio">
			<dl>
				<dt>
					<a rel="foaf:page" href="{$albums[i]->getURL()}">
						<span{if $albums[i]->image != false} about="{$albums[i]->id}" rel="foaf:depiction"{/if}>
							<img class="album photo" {if $albums[i]->image == false} src="{$base_url}/i/qm160.png"{else}src="{$albums[i]->image}"{/if}
							alt="{$albums[i]->name|escape:'html':'UTF-8'}"
							title="{$albums[i]->name|escape:'html':'UTF-8'}" width="160" />
						</span>
					</a>
				</dt>
				<dd class="description" property="rdfs:comment">{$albums[i]->getPlayCount()} plays</dd>
			</dl>
		</li>
		{/section}
	</ul>

</div>


<div class="cleaner">&nbsp;</div>

{include file='footer.tpl'}

