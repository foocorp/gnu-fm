{include file='header.tpl'}

<h2>Invite</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($sent)}
	<p>Your invitation has been sent, pretty soon your friend should be thanking you profusely.</p>
{else}

<div id='invite'>

	<form action='' method='post'>
		<fieldset>
	        <p>Do you have an awesome friend you'd like to share <a href='http://libre.fm' rel='bookmark' class='vcard fn org url'>libre.fm</a> with? Just enter his/her email address and we'll sort them out with an invitation code.</p>

			<label for='email'>Invitee's E-mail:<span>must be valid!</span></label>
			<input id='email' name='email' type='text' value='' />

			<input type='submit' name='invite' value='Let them in!'/>
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
