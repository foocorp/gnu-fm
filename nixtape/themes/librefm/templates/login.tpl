{include file='header.tpl'}

<h2>Login</h2>

<div id="register-prompt">
<h3>Need an account? <a href="register.php">Register now!</a></h3>
<p>It's free, easy and takes only a few seconds...</p>
</div>

{if !empty($errors)}
<div id='errors'>
	<p>{$errors}</p>
    {if isset($invalid)}
	<p><a href="{$base_url}/reset.php"><strong>{t}Reset your password{/t}</a></strong></p>
    {/if}
</div>
{/if}

<div id='login-form'>
	<form action='' method='post'>
		<fieldset>
			<label for='username'>{t}Username{/t}<span>&nbsp;</span></label>
			<input id='username' name='username' type='text' value='{$username}' maxlength='64' />

			<label for='password'>{t}Password{/t}<span>&nbsp;</span></label>
			<input id='password' name='password' type='password' value=''/>


			
			<label for='remember'>{t}Remember me{/t}<span>&nbsp;</span></label>
			<input id='remember' name='remember' type='checkbox' value='1'/>
			
			<input type='submit' name='login' value='{t}Let me in!{/t}' />
			<input name="return" type="hidden" value="{$return|htmlentities}" />
			
		</fieldset>

	</form>
</div>

{include file='footer.tpl'}
