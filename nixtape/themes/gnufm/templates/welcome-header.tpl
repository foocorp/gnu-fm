{if $logged_in}
<div id="welcome-box">
	<h1>{t}Welcome back!{/t}</h1>
</div>

{else}
<div id='site-title'><h1><a href="{$base_url}">{$site_name}</a></h1></div>
{/if}
