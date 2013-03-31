{include file='header.tpl'}

<h2 property="dc:title">{$me->name|escape:'html':'UTF-8'}'{if $me->name|substr:-1 != 's'}s{/if} journal</h2>

{include file='maxiprofile.tpl'}

<ul about="{$me->id}" rel="foaf:made" rev="foaf:maker" class="hfeed">
{foreach from=$items item=i}
	<li {if $i.subject_uri}about="{$i.subject_uri|escape:'html':'UTF-8'}" {/if}typeof="sioc:Item rss:item" class="hentry">
		<b class="entry-title" property="dc:title">{$i.title|escape:'html':'UTF-8'}</b><br />
		<a property="rss:item" rel="bookmark sioc:link" href="{$i.link|escape:'html':'UTF-8'}">{$i.link|escape:'html':'UTF-8'}</a>
		<abbr class="published" property="dc:date" content="{$i.date_iso}" title="{$i.date_iso}">{$i.date_human}</abbr>
	</li>
{/foreach}
</ul>
{include file='footer.tpl'}
