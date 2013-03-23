<div id="profile-box">
	{if $me->fullname}
	<h2>{$me->fullname|escape:'html':'UTF-8'}</h2>
	{else}
	<h2>{$me->name|escape:'html':'UTF-8'}</h2>	
	{/if}

{if $me->homepage}
		<p><a href="{$me->homepage|escape:'html':'UTF-8'}" rel="me foaf:homepage" class="url"><img src="{$me->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" /></a></p>
{else}

		<p><img src="{$me->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" /></p>
{/if}

	<p>
</div>
