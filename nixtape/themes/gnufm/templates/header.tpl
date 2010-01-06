<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>GNU FM</title>
	<meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
{section name=i loop=$extra_head_links}
	<link rel="{$extra_head_links[i].rel|escape:'html':'UTF-8'}" href="{$extra_head_links[i].href|escape:'html':'UTF-8'}" type="{$extra_head_links[i].type|escape:'html':'UTF-8'}" title="{$extra_head_links[i].title|escape:'html':'UTF-8'}"  />
{/section}
</head>

<body>
<h1><a href="/">GNU FM</a></h1>
