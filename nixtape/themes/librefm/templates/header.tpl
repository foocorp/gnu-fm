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
	<title>Libre.fm &mdash; {t}discover new music{/t}</title>
	<meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/reset-fonts-grids.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/base.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/alpha.css" type="text/css" />
	<link rel="icon" href="{$base_url}/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="{$base_url}/js/player.js"></script>
	<script type="text/javascript" src="{$base_url}/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="{$base_url}/js/jquery-ui-1.7.1.custom.min.js"></script>
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'html':'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
</head>

<body typeof="foaf:Document">

<div id="project-links">

<ul>
<li><a href="http://libre.fm/">Libre.fm</a></li>
<li><a href="http://libre.fm#what/">What is Libre.fm?</a></li>
<li><a href="http://libre.fm#why/">Why use Libre.fm?</a></li>
<li><a href="http://libre.fm#artists/">What's in it for artists?</a></li>
<li><a href="http://libre.fm#users/">What's in it for users?</a></li>
</ul>

</div>

	<div id="hd" role="navigation">
	  <div class="inner">
		<h1 rel="dc:publisher" class="vcard"><a property="foaf:name" rel="foaf:homepage" href="{$base_url}" class="fn org url">Libre.fm</a></h1>
		{include file='menu.tpl'}
           </div>
	</div>

   <div id="bd" role="main">
<div class="inner yui-t7">  
    <div class="yui-gc">
