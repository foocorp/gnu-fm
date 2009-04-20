{include file='header.tpl'}

<h2>Password recovery</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($sent)}
	<p>An email with further information has been sent to the email address associated with your profile.</p>
	
{else}

<div id='invite'>

	<form action='' method='post'>
		<fieldset>
	        <p><a href='http://libre.fm' rel='bookmark' class='vcard fn org url'>libre.fm</a> Password Recovery</p>

			<label for='username'>Username <span>must be valid!</span></label>
			<input id='username' name='user' type='text' value='' />

			<input type='submit' name='recover' value='Recover my password!'/>
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
