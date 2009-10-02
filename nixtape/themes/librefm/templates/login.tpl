{include file='header.tpl'}

<h2>Login</h2>

<h3>Need an account? <a href="register.php">Register now!</a></h3>


{if !empty($errors)}
<div id='errors'>
	<p>{$errors}</p>
    {if isset($invalid)}
	<p><a href="{$base_url}/reset.php"><strong>{t}Reset your password{/t}</a></strong></p>
    {/if}
</div>
{/if}

</div>

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


	<h2>Like OpenID?</h2>

	<p>So do we. We'll be adding OpenID support in the next few days.</p>

	<p>Join us in #libre.fm on irc.freenode.net to help shape
	that, or <a
	href="http://lists.autonomo.us/mailman/listinfo/libre-fm">join
	our mailing list</a> and have your say!</p>

    {include file='privacy.tpl'}

{include file='footer.tpl'}
