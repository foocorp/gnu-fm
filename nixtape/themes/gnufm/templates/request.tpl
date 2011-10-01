{include file='header.tpl'}

<h2>Request invite</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($reg)}
	<p>{t}Your request for an invitation has been registered. Thank you for your interest in libre.fm!{/t}</p>
{else}

<div id='invite'>

	<form action='' method='post'>
		<fieldset>
	        <p>{t escape=no}<a href='http://libre.fm' rel='bookmark' class='vcard fn org url'>libre.fm</a> is now in alpha. We're slowly adding new users, so if you're interested, type in your email address and you'll receive an invitation in a few days time.{/t}</p>

			<label for='email'>{t}Email{/t} <span>{t}must be valid!{/t}</span></label>
			<input id='email' name='email' type='text' value='' />

			<input type='submit' name='request' value='{t}Request invite!{/t}'/>
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
