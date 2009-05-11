{include file='header.tpl'}

<h2>Login</h2>

{if !empty($errors)}
	<p id='errors'>{$errors}</p>
    {if isset($invalid)}
	<a href="{$base_url}/reset.php">{t}Lost password{/t}?</a><br />
    {/if}
{/if}

<div id='login'>
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
