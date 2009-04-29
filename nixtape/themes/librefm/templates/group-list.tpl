{include file='header.tpl'}

<h2 property="dc:title">All Groups</h2>

<div about="#groups" typeof="foaf:Group" property="foaf:name" content="All Groups">

<ul rel="foaf:member" class="userlist">
{foreach from=$groups item=g}

	<li about="{$g->id}" typeof="foaf:Group">
		<div class="group vcard">
			<div class="avatar" rel="foaf:depiction">
				<img src="{$g->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" />
			</div>
			<dl>
				<dt>
					<span class="fn" property="foaf:name">{$g->fullname|escape:'html':'UTF-8'}</span>
					(<span class="nickname" property="foaf:nick">{$g->name|escape:'html':'UTF-8'}</span>)
				</dt>
				<dd>{if $g->homepage}<a class="url" rel="foaf:page" href="{$g->homepage|escape:'html':'UTF-8'}">{$g->homepage|escape:'html':'UTF-8'}</a>{/if}</dd>
				<dd class="note" property="dc:abstract">{$g->bio|escape:'html':'UTF-8'}</dd>
				<dd><a rel="foaf:homepage" rev="foaf:primaryTopic" property="dc:description" href="{$g->getURL()|escape:'html':'UTF-8'}">{$g->count} users</a></dd>
			</dl>
			<hr style="border: 1px solid transparent; clear: both;" />
		</div>
	</li>
	
{/foreach}
</ul>

</div>


<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>Top artists</h3>
<ul class="tagcloud" about="{$id}">
	{section name=i loop=$group_tagcloud}
	<li style="font-size:{$group_tagcloud[i].size}"><a
	href="{$group_tagcloud[i].pageurl|escape:'html':'UTF-8'}" rel="{if $group_tagcloud[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$group_tagcloud[i].artist|escape:"html":"UTF-8"}</a></li>
	{/section}
</ul>

	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '0';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->



{include file='footer.tpl'}
