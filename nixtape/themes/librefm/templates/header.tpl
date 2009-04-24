<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<!-- @role doesn't validate with this DTD, but is useful for accessibility -->
<html
	version="XHTML+RDFa 1.0"
	xmlns="http://www.w3.org/1999/xhtml"
	xmlns:bio="http://purl.org/vocab/bio/0.1/"
	xmlns:dc="http://purl.org/dc/terms/"
	xmlns:foaf="http://xmlns.com/foaf/0.1/"
	xmlns:gob="http://purl.org/ontology/last-fm/"
	xmlns:mo="http://purl.org/ontology/mo/"
	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	xmlns:sioc="http://rdfs.org/sioc/ns#"
	xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
	xmlns:rss="http://purl.org/rss/1.0/"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xml:lang="en">
	
<head profile="http://www.w3.org/1999/xhtml/vocab http://purl.org/uF/2008/03/ http://purl.org/uF/hAudio/0.9/">
	<title>Libre.fm &mdash; discover new music</title>
	<link rel="stylesheet" href="{$base_url}/themes/librefm/reset-fonts-grids.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/base.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/librefm.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/alpha.css" type="text/css" />
	<link rel="icon" href="{$base_url}/favicon.ico" type="image/x-icon" />
	<link rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/us/" />
	<script type="text/javascript" src="{$base_url}/js/player.js"></script>
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel}" href="{$extra_head_links[i].href}" type="{$extra_head_links[i].type}" title="{$extra_head_links[i].title}"  />
{/section}
</head>

<body typeof="foaf:Document">
<div id="doc2" class="yui-t7">
	<div id="hd" role="navigation">
		<h1 rel="dc:publisher" class="vcard"><a property="foaf:name" rel="foaf:homepage" href="{$base_url}" class="fn org url">Libre.fm</a></h1>
		{include file='menu.tpl'}
	</div>

   <div id="bd" role="main">
   <div id="coolio">
	{if ($logged_in)}
	<!-- put something here -->
        {else}
	{if $welcome}
	<div class="yui-g" id="banner">     
	  <a href="{$base_url}/register.php"><img src="{$base_url}/i/topblock.png" alt="" /></a>
	{else}
	<div class="yui-g">     
	  <a href="{$base_url}/register.php"><img src="{$base_url}/themes/librefm/images/topblocksmall.png" alt="" /></a>
	{/if}
	</div>{/if}
<div class="yui-gc">
    <div class="yui-u first" id="content">
    <div style="padding: 10px;">
