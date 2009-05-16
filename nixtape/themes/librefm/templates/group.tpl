{include file='header.tpl'}

<h2 property="dc:title">{t}Group:{/t} {$fullname|escape:'html':'UTF-8'}</h2>

<div about="{$id}" typeof="foaf:Group">

<div class="group vcard">
	<div class="avatar" rel="foaf:depiction">
		<img src="{$avatar|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" />
	</div>

	{if $logged_in}
		{if $ismember}
			{if $isowner}
			<a class="edit" href="{$link_edit}">[{t}edit{/t}]</a>
			{else}
			<form class="edit" action="{$link}" method="get"><div><input type="hidden" name="action" value="leave" /><input type="submit" value=" {t}leave{/t} " /></div></form>
			{/if}
		{else}
			<form class="edit" action="{$link}" method="get"><div><input type="hidden" name="action" value="join" /><input type="submit" value=" {t}join{/t} " /></div></form>
		{/if}
	{/if}

	<dl>
		<dt>
			<span class="fn org" property="foaf:name">{$fullname|escape:'html':'UTF-8'}</span>
			(<span class="nickname" property="foaf:nick">{$group|escape:'html':'UTF-8'}</span>)
		</dt>
		<dd>{if $homepage}<a class="url" rel="foaf:page" href="{$homepage|escape:'html':'UTF-8'}">{$homepage|escape:'html':'UTF-8'}</a>{/if}</dd>
		<dd class="note" property="dc:abstract">{$bio|escape:'html':'UTF-8'}</dd>
	</dl>

	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:homepage" rev="foaf:primaryTopic" resource="" />
</div>

<ul rel="foaf:member" class="userlist">
{foreach from=$userlist item=me}

	<li>{include file='miniprofile.tpl'}</li>
	
{/foreach}
</ul>

</div>


<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{t}Top artists{/t}</h3>
<ul class="tagcloud" about="{$id}">
	{section name=i loop=$group_tagcloud}
	<li style="font-size:{$group_tagcloud[i].size}"><a
	href="{$group_tagcloud[i].pageurl|escape:'html':'UTF-8'}" rel="{if $group_tagcloud[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$group_tagcloud[i].artist|escape:"html":"UTF-8"}</a></li>
	{/section}
</ul>

{if !isset($this_user) || $this_user->anticommercial<>1}
	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '0';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->
{/fi}



{include file='footer.tpl'}
