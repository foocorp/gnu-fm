<!DOCTYPE html>
<html>
<head>
	{if !($pagetitle)}
	<title>Libre.fm &mdash; {t}discover new music{/t}</title>
	{else}
	<title>{$pagetitle|escape:'html':'UTF-8'} &mdash; Libre.fm &mdash; {t}discover new music{/t}</title>
	{/if}
   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css" />
   <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/base/base.css" type="text/css" />
   <link rel="stylesheet" href="http://s.libre.fm/librefm/css/tmp.css" type="text/css" />
	<meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'html':'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
</head>

<body>

<div id="doc3" class="yui-t2">
	<div id="hd">
		<h1>{if ($logged_in)}
	<a href="{$this_user->getURL()}">{$this_user->name}</a>'s&nbsp;
	{/if}
<a href="{$base_url}">Libre.fm</a></h1>


		{include file='menu.tpl'}
	</div>

   <div id="bd">
<div id="yui-main">
	<div class="yui-b"><div class="yui-ge">
    <div class="yui-u first">

<p><b>We're doing some work on the site, regular design will return shortly.</b></p>

{if !empty($errors)}
<div id="errors">
	<p>{$errors}</p>
</div>
{/if}
