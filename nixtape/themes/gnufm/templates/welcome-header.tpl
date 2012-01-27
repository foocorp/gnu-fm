{if $logged_in}
<div id="welcome-box">
	<h2>{t}Welcome back!{/t}</h2>
</div>

{else}
<center><div id='site-title'><h2><a href="{$base_url}">{$site_name}</a></h2></div></center>
{/if}
