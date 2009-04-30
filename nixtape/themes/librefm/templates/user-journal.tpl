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

<!-- Column break -->
</div></div><div class="yui-u" id="sidebar"><div style="padding: 10px;">

<h3>{$me->name}'s top artists</h3>
<ul class="tagcloud" about="{$me->id}">
	{section name=i loop=$user_tagcloud}
	<li style="font-size:{$user_tagcloud[i].size}"><a
	href="{$user_tagcloud[i].pageurl|escape:'html':'UTF-8'}" rel="{if $user_tagcloud[i].size|substr:-5 ==
	'large'}foaf:interest {/if}tag">{$user_tagcloud[i].artist|escape:"html":"UTF-8"}</a></li>
	{/section}
</ul>

	<div id="adbard">

	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '55fd9cde6d855a75f9ca43d854272f6b';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->

	</div>

{include file='footer.tpl'}
