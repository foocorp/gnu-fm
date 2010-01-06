{include file='header.tpl'}

<h2 property="dc:title">{t}Country:{/t} {$country_info.country_name|escape:'html':'UTF-8'}</h2>

<div about="#usergroup" typeof="foaf:Group">

<div class="group vcard">
	<dl>
		<dt>
			<span property="foaf:name">{t}Users in{/t} {$country_info.country_name|escape:'html':'UTF-8'}</span>
			(<span class="nickname" property="foaf:nick">{$country_info.country|escape:'html':'UTF-8'}</span>)
		</dt>
		<dd>{if $country_info.wikipedia_en}<a class="url" rel="foaf:page" href="{$country_info.wikipedia_en|escape:'html':'UTF-8'}">{$country_info.wikipedia_en|escape:'html':'UTF-8'}</a>{/if}</dd>
	</dl>
	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:homepage" rev="foaf:primaryTopic" resource="" />
</div>

<ul rel="foaf:member" class="userlist">
{foreach from=$userlist item=me}

	<li>{include file='miniprofile.tpl'}</li>
	
{/foreach}
</ul>

</div>

<div class='cleaner'>&nbsp;</div>
{include file='footer.tpl'}
