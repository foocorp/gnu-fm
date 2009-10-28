{include file='header.tpl'}

{if isset($activated)}

	<h3>{t}Your account has been activated! You may now login!{/t}</h3>

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

			<label for='username'>{t}Your username:{/t}<span>{t}(no more than 16 chars.){/t}</span></label>
			<input id='username' name='username' type='text' value='{$username}' maxlength='16' />

			<label for='password'>{t}Your password:{/t}<span>{t}(make it hard to guess){/t}</span></label>
			<input id='password' name='password' type='password' value=''/>

			<label for='password-repeat'>{t}Your password again{/t}<span>{t}(you should repeat it.){/t}</span></label>
			<input id='password-repeat' name='password-repeat' type='password' value=''/>

			<label for='email'>{t}Your e-mail:{/t}<span>{t}(must be valid!){/t}</span></label>
			<input id='email' name='email' type='text' value='{$email}' maxlength='64' />

		</fieldset>

		<input type='submit' name='register' value="{t}Sign up{/t}" />

	</form>

	<p class="disclaimer">{t}We won't sell, swap or give away your email address. You can optionally include personal data on your profile, which is displayed publicly.{/t}</p>
	
{/if}
{include file='footer.tpl'}
