<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	{if !($pagetitle)}
		<title>{$site_name}</title>
	{else}
		<title>{$pagetitle|escape:'html':'UTF-8'} &mdash; {$site_name}</title>
	{/if}
	<link rel="stylesheet" href="{$base_url}/themes/{$default_theme}/css/modern.css" type="text/css" />
	<script type="text/javascript" src="{$base_url}/js/jquery.min.js"></script>
	<script type="text/javascript" src="{$base_url}/js/jquery-ui.custom.min.js"></script>
	<script type="text/javascript" src="{$base_url}/js/jquery.placeholdr.js"></script>
	<script type="text/javascript">
		var base_url="{$base_url}";
	</script>
	<script type="text/javascript" src="{$base_url}/js/player.js"></script>
        <script type="text/javascript" src="{$base_url}/themes/{$default_theme}/js/modernizr.js"></script>
	<meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
	<meta name="viewport" content="width=device-width,initial-scale=1">
</head>

<body>
  <div id="header-container">
    <header class="wrapper clearfix">
      <h1 id="title"><a href="{$base_url}">{$site_name}</a></h1>
      <nav>
	{include file='menu.tpl'}
      </nav>
      </header>
   </div>

  <div id="main-container">
    <div id="main" class="wrapper clearfix">
      
      <article>
	<header>
				{if isset($headerfile)}
					{include file="$headerfile"}
				{/if}
				{if isset($pageheading)}
					<h1 id="page-title" class="asset-name">{$pageheading}</h1>
				{/if}
				{include file='submenu.tpl'}
			</header>
			<section>
