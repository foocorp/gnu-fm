{include file='header.tpl'}

<ul>
{section name=i loop=$userlist}
<li><a href="{$userlist[i].username|escape:'html':'UTF-8'}">{$userlist[i].username|escape:'html':'UTF-8'}</li>
{/section}
</ul>

{include file='footer.tpl'}

