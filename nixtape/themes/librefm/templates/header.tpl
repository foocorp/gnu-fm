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
	{if !($pagetitle)}
	<title>Libre.fm &mdash; {t}discover new music{/t}</title>
	{else}
	<title>{$pagetitle|escape:'html':'UTF-8'} &mdash; Libre.fm &mdash; {t}discover new music{/t}</title>
	{/if}
	<meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
	<link rel="stylesheet" href="{$media_url}/{$default_theme}/css/r.css" type="text/css" />
	<link rel="stylesheet" href="{$media_url}/{$default_theme}/css/b.css" type="text/css" />
	<link rel="stylesheet" href="{$media_url}/{$default_theme}/css/a.css" type="text/css" />
	<link rel="stylesheet" href="{$media_url}/{$default_theme}/css/new.css" type="text/css" />
	<link rel="icon" href="{$media_url}/{$default_theme}/img/favicon.ico" type="image/x-icon" />
	<script type="text/javascript" src="{$media_url}/js/player.min.js"></script>
	<script type="text/javascript" src="{$media_url}/js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="{$media_url}/js/jquery-ui-1.7.1.custom.min.js"></script>
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'html':'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
</head>

<body typeof="foaf:Document">

<div id="doc2" class="yui-t7">
	<div id="hd" role="navigation">
		<h1 rel="dc:publisher" class="vcard"><a property="foaf:name" rel="foaf:homepage" href="{$base_url}" class="fn org url">Libre.fm</a></h1>
		{include file='menu.tpl'}
	</div>

   <div id="bd" role="main">
<div class="yui-g">

{if !empty($errors)}
<div id='errors'>
	<p>{$errors}</p>
</div>
{/if}
