{include file='header.tpl'}

{if isset($error)}
	<p>{t}Sorry, the link you followed does not appear to be correct. Please make sure you copied the entire link from your mail client. If this still doesn't work you can change your subscribption options at any time from your profile page by logging in, visiting your profile and clicking the "Edit" link.{/t}</p>
{else}
	<p>{t}You've now been unsubscribed from our e-mails, if you'd ever like to subscribe again simply log in, visit your profile and click the "Edit" link.{/t}</p>
{/if}
