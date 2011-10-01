{if $logged_in}
<div id="welcome-box">
	<h2>{t}Welcome back!{/t}</h2>
</div>

{else}
<h2><img src="{$img_url}/welcome-message.gif" alt="{t}Libre.fm allows you to share your listening habits and discover new music.{/t}" /></h2>
{/if}
