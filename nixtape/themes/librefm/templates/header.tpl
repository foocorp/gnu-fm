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
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	xml:lang="en">
	
<head profile="http://www.w3.org/1999/xhtml/vocab http://purl.org/uF/2008/03/ http://purl.org/uF/hAudio/0.9/">
	<title>Libre.fm &mdash; discover new music</title>
	<link rel="stylesheet" href="{$base_url}/themes/librefm/reset-fonts-grids.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/base.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/librefm.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/alpha.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/player.css" type="text/css" />
	<link rel="icon" href="{$base_url}/favicon.ico" type="image/x-icon" />
	<link rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/us/" />
	<script type="text/javascript" src="{$base_url}/js/player.js"></script>
	<script type="text/javascript" src="{$base_url}/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="{$base_url}/js/jquery-ui-1.7.1.custom.min.js"></script>
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'html':'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
</head>

<body typeof="foaf:Document">

<div id="try-the-alpha"><a href="http://alpha.libre.fm{$this_page}">This is the current, live, in-development beta version of the site</a></div>

{if ($sidebar)}<div id="doc2" class="yui-t5">{else}<div id="doc2" class="yui-t7"> {/if}
	<div id="hd" role="navigation">
		<h1 rel="dc:publisher" class="vcard"><a property="foaf:name" rel="foaf:homepage" href="{$base_url}" class="fn org url">Libre.fm</a></h1>
		{include file='menu.tpl'}
	</div>


  {if ($sidebar)}   <div id="bd" role="main"><div id="yui-main"> 
  <div class="yui-b"><div class="yui-g">{else}
   <div id="bd" role="main"> 
    <div class="yui-g">{/if}
