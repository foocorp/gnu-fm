{include file='header.tpl'}

<h2>Request invite</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($reg)}
	<p>{t}Your request for an invitation has been registered. Thank you for your interest!{/t}</p>
{else}

<div id='invite'>

	<form action='' method='post'>
		<fieldset>

			<label for='email'>{t}Email{/t} <span>{t}must be valid!{/t}</span></label>
			<input id='email' name='email' type='text' value='' />

			<input type='submit' name='request' value='{t}Request invite!{/t}'/>
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
