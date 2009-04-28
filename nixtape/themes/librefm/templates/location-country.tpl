{include file='header.tpl'}

<h2 property="dc:title">A Little Country Called "{$country}" (for now)</h2>

<div about="#usergroup" typeof="foaf:Group" property="foaf:name" content="Users in {$country}">

<ul rel="foaf:member" class="userlist">
{section name=i loop=$userlist}

	<li>{include file='miniprofile.tpl'}</li>
	
{/section}
</ul>

</div>

<div class='cleaner'>&nbsp;</div>
{include file='footer.tpl'}
