{include file='mini-header.tpl'}

{if isset($activated)}

        <h2>{t}You're in!{/t}</h2>

	<p>{t escape=no}Your account has been activated! You may now <a href="{$base_url}/login.php">login!</a>{/t}</p>

{elseif isset($registered)}

	<h2>{t}Go! Go! Go! Check your email now{/t}</h2>

	<p>{t}Please follow the link in your email to activate your account!{/t}</p>
	
{else}

<h2>{t}You look awesome today, by the way{/t}</h2>

	{if isset($errors)}
		<p id='errors'>{$errors}</p>
	{/if}

	<form action='' method='post'>
		<fieldset>

			<h3><label for='username'>{t}Your username:{/t}</label></h3>
			<p><small>{t}No more than 16 characters, please.{/t}</small></p>
			<div><input id='username' name='username' type='text' value='{$username}' maxlength='16' size='16' /></div>

			<h3>
			<label for='password'>{t}Your password:{/t}</label></h3>
			<p><small>{t}Try and make it hard to guess! Don't use the same password for everything!{/t}</small></p>
			<div><input id='password' name='password' type='password' value=''/></div>

			<h3>
			<label for='password-repeat'>{t}Your password again{/t}</label></h3>
			<p><small>{t}Who said repeating yourself was a bad thing?{/t}</small></p>
			<div><input id='password-repeat' name='password-repeat' type='password' value=''/></div>

			<h3><label for='email'>{t}Your e-mail:{/t}</label></h3>
			<p><small>{t}We're going to email you here to confirm this account.{/t}</small></p>
			<div><input id='email' name='email' type='text' value='{$email}' maxlength='64' /></div>

			<p><label><input type="checkbox" name="foo-check" /> {t}I read this form carefully, and double-checked my email address first, honest.{/t}</label></p>

		</fieldset>

		<p><input type='submit' name='register' value="{t}Sign up{/t}" /></p>

	</form>

	<p><small>{t}We won't sell, swap or give away your email address. You can optionally include personal data on your profile, which is displayed publicly.{/t}</small></p>
	
{/if}
{include file='mini-footer.tpl'}
