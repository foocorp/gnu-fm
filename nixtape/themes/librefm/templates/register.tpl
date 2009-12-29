{include file='mini-header.tpl'}

{if isset($activated)}

        <h2>You're in!</h2>

	<p>Your account has been activated! You may now <a href="/login.php">login!</a></p>

{elseif isset($registered) }

	<h2>Go! Go! Go! Check your email now</h2>

	<p>{t}Please follow the link in your email to activate your account!{/t}</p>
	
{else}

<h2>You look awesome today, by the way</h2>

	{if isset($errors) }
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
			<p><small>Who said repeating yourself was bad thing?</small></p>
			<div><input id='password-repeat' name='password-repeat' type='password' value=''/></div>

			<h3><label for='email'>{t}Your e-mail:{/t}</label></h3>
			<p><small>We're going to email you here to confirm this account, first.</small></p>
			<div><input id='email' name='email' type='text' value='{$email}' maxlength='64' /></div>

		</fieldset>

		<p><input type='submit' name='register' value="{t}Sign up{/t}" /></p>

	</form>

	<p><small>{t}We won't sell, swap or give away your email address. You can optionally include personal data on your profile, which is displayed publicly.{/t}</small></p>
	
{/if}
{include file='mini-footer.tpl'}
