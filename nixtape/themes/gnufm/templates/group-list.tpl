{include file='header.tpl'}

<h2 property="dc:title">{t}All Groups{/t}</h2>

<div about="#groups" typeof="foaf:Group" property="foaf:name" content="{t}All Groups{/t}">

<ul rel="foaf:member" class="userlist">
{foreach from=$groups item=g}

	<li about="{$g->id}" typeof="foaf:Group" class="vcard">
		<span rel="foaf:depiction"><img src="{$g->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="48" height="48" /></span>
		<a class="fn org url"
			rel="foaf:homepage" rev="foaf:primaryTopic" href="{$g->getURL()|escape:'html':'UTF-8'}"
			property="foaf:name">{$g->fullname|escape:'html':'UTF-8'}</a>
		&mdash;
		<span class="note" property="dc:description">{t members='$g->count}%1 members{/t}</span>
	</li>
{/foreach}
</ul>

</div>

{include file='footer.tpl'}
