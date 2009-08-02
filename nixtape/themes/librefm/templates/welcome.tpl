{include file='header.tpl'}
{if ($logged_in)}

<h2>Hey <a href="{$this_user->getURL()}">{$this_user->name}</a>!</h2>

<p>Some of the changes we have coming up in the next few weeks that we'd like your feedback on:-</p>

{include file='features.tpl'}

<p>You can send your feedback to our <a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">mailing list</a>, which you should join.</p>

{else}

<h2 style="font-size: 24px; color: red;">Libre.fm lets you discover new music and share your listening habits with your friends.</h2>

<h2>Sign up now. It's free, quick and easy.</h2>

<form action="{$base_url}/register.php" method="post">
{include file='register-form.tpl'}
</form>

{/if}

{include file='footer.tpl'}
