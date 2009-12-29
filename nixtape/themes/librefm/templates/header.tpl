<!DOCTYPE html>
<html>
<head>
	{if !($pagetitle)}
	<title>Libre.fm &mdash; {t}discover new music{/t}</title>
	{else}
	<title>{$pagetitle|escape:'html':'UTF-8'} &mdash; Libre.fm &mdash; {t}discover new music{/t}</title>
	{/if}
   <link rel="stylesheet" href="http://s.libre.fm/librefm/css/tmp.css?200912281344" type="text/css" />
   <script type="text/javascript" src="http://s.libre.fm/librefm/js/js.js?200912281344"></script>
	<meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'html':'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
</head>

<body>

<div id="doc3" class="yui-t7">
	<div id="hd">
		{if ($logged_in)}
	<p><a href="{$this_user->getURL()}">{$this_user->name}</a>'s</p>
	{/if}
<h1><a href="{$base_url}">Libre.fm</a></h1>



	</div>

   <div id="bd">
<div class="yui-ge">
    <div class="yui-u first">
