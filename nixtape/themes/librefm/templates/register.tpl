{include file='header.tpl'}

{if isset($activated)}

	<h3>Your account has been activated! You may now <a href="/login.php">login!</a></h3>

{elseif isset($registered) }

	<h2><img src="http://s.libre.fm/librefm/img/check-mail.png" alt="Go! Go! Go! Check your email now" /></h2>

	<h3>{t}Please follow the link in your email to activate your account!{/t}</h3>
	
{else}

<h2><img src="http://s.libre.fm/librefm/img/look-awesome.png" alt="You look awesome today by the way" /></h2>

	{if isset($errors) }
		<p id='errors'>{$errors}</p>
	{/if}

	<form action='' method='post'>
		<fieldset>

			<div><label for='username'>{t}Your username:{/t}<span>{t}(no more than 16 chars.){/t}</span></label>
			<input id='username' name='username' type='text' value='{$username}' maxlength='16' /></div>

			<div>
			<label for='password'>{t}Your password:{/t}<span>{t}(make it hard to guess){/t}</span></label>
			<input id='password' name='password' type='password' value=''/></div>

			<div>
			<label for='password-repeat'>{t}Your password again{/t}<span>{t}(you should repeat it.){/t}</span></label>
			<input id='password-repeat' name='password-repeat' type='password' value=''/></div>

			<div><label for='email'>{t}Your e-mail:{/t}<span>{t}(must be valid!){/t}</span></label>
			<input id='email' name='email' type='text' value='{$email}' maxlength='64' /></div>

		</fieldset>

		<p><input type='submit' name='register' value="{t}Sign up{/t}" /></p>

	</form>

	<h3 class="disclaimer">{t}We won't sell, swap or give away your email address. You can optionally include personal data on your profile, which is displayed publicly.{/t}</h3>
	
{/if}
{include file='footer.tpl'}
