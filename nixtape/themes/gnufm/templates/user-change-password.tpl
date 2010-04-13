{include file='header.tpl'}

{if isset($errors)}
<div id="errors">
{section loop=$errors name=error}
	<p>{$errors[error]}</p>
{/section}
</div>
{/if}

{if isset($success)}
<div id="success">
	<p>Password was changed successfully!</p>
</div>
{/if}
<div id='change-password'>
	<h2 property='dc:title'>Change your password</h2>
	<form action='{$base_url}/user-change-password.php' method='post'>
	<div><h3><label for='new_password'>{t}New password:{/t}</h3>
		<div class='formHelp'>Enter the new desired password here</div>
		<input name='password1' id='password' type='password' />
	</div>
	<div><h3><label for='new_password_repeat'>{t}Repeat new password:{/t}</h3>
		<div class='formHelp'>Repeat the new password</div>
		<input name='password2' id='password' type='password' />
	</div>
	<div><h3><label for='old_password'>{t}Old password (for verification){/t}</h3>
		<div class='formHelp'>Type your old password</div>
		<input name='old_password' id='password' type='password' />
	</div>
	<p>
		<input name='submit' value='1' type='hidden' />
		<input type='submit' value='Change'/>
	</p>
	</form>
</div>

{include file='footer.tpl'}
