{include file='header.tpl'}

<h2>Invite</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($sent)}
	<p>Your invitation has been sent, pretty soon your friend should be thanking you profusely.</p>
{else}

<div id="invite">

	<p>Do you have an awesome friend you'd like to share libre.fm with? Just enter their email address and we'll sort them out with an invitation code.</p>

	<form action='' method='post'>
		<fieldset>
			<label for='email'>Invitee's E-mail:</label>
			<input id='email' name='email' type='text' value='' /><br />

			<input type='submit' name='invite' value="Let them in!" />
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
