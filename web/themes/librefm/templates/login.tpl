{include file='header.tpl'}

<h2>Login</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

<div id="login">
	<form action='' method='post'>
		<fieldset>
			<label for='username'>Username:</label>
			<input id='username' name='username' type='text' value='{$username}' maxlength='64' />

			<label for='password'>Your password:</label>
			<input id='password' name='password' type='password' value=''/>

			<input type='submit' name='login' value="Let me in!" />
		</fieldset>

	</form>
</div>

{include file='footer.tpl'}
