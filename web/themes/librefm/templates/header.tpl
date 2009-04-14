<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
    "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
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
	xml:lang="en">
	
<head profile="http://www.w3.org/1999/xhtml/vocab">
	<title>Libre.fm &mdash; discover new music</title>
	<link rel="stylesheet" href="http://turtle.libre.fm/reset-fonts-grids.css" type="text/css" />
	<link rel="stylesheet" href="http://turtle.libre.fm/base.css" type="text/css" />
	<link rel="stylesheet" href="http://turtle.libre.fm/librefm.css" type="text/css" />
	<link rel="stylesheet" href="{$base_url}/themes/librefm/alpha.css" type="text/css" />
	<link rel="icon" href="{$base_url}/favicon.ico" type="image/x-icon">
	<script type="text/javascript" src="{$base_url}/js/player.js"></script>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="license" href="http://creativecommons.org/licenses/by-sa/3.0/us/" />
</head>

<body>
<div id="doc2" class="yui-t7">
	<div id="hd" role="navigation" class='vcard'><h1 class='fn org'><a href="{$base_url}" class='url'>Libre.fm</a></h1>
	{include file='menu.tpl'}
	</div>
   <div id="bd" role="main">
   <div id="coolio">
	{if ($logged_in)}
	<!-- put something here -->
        {else}
	{if $welcome}
	<div class="yui-g" id="banner">     
	  <a href="/request.php"><img src="http://libre.fm/i/topblock.png" alt="" /></a>
	{else}
	<div class="yui-g">     
	  <a href="/request.php"><img src="http://alpha.libre.fm/themes/librefm/images/topblocksmall.png" alt="" /></a>
	{/if}
	</div>{/if}
<div class="yui-gc">
    <div class="yui-u first" id="content">
    <div style="padding: 10px;">
