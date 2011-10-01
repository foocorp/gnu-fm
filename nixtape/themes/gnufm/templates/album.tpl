{include file='header.tpl'}
<div about="{$id}" typeof="mo:Record" class="haudio">

	<div{if $album->image != false} rel="foaf:depiction"{/if}>
		<img class="albumart" {if $album->image == false} src="{$img_url}/qm160.png"{else} src="{$album->image}"{/if}
			alt="{$album->name|escape:'html':'UTF-8'}" title="{$album->name|escape:'html':'UTF-8'}" width="160" />
	</div>

<ul id="tracks" rel="mo:track">
	{section name=i loop=$tracks}
	<li about="{$tracks[i]->id}" typeof="mo:Track" class="item">
		<a class="fn url" href="{$tracks[i]->getURL()}" rel="foaf:page" property="dc:title">{$tracks[i]->name}</a>
	</li>
	{/section}
	{if $add_track_link}<li><a href='{$add_track_link}'><strong>[{t}Add new track{/t}]</strong></a></li>{/if}
</ul>
</div>

<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
