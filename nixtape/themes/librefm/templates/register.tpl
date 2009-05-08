{include file='header.tpl'}

<h2>Register</h2>

{if isset($activated)}
	<h2>{t}Your account has been activated! You may now login!{/t}</h2>

{elseif isset($registered) }
	<h2>{t}You're now registered with libre.fm! An email has been sent to the email address you provided. Please follow the link in the email to activate your account!{/t}</h2>
	
	<ul>
	<li><a href="http://ideas.libre.fm/index.php/Using_turtle">{t}Find out how to start sending us your listening habits{/t}</a></li>
	<li><a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">{t}Join our mailing list{/t}</a></li>
	<li><a href="http://blog.libre.fm/">{t}Read our blog{/t}</a> {t}and{/t} <a href="http://identi.ca/mattl">{t}subscribe to Matt (our founder) on identi.ca{/t}</a></li>
	</ul>


{else}

	{if isset($errors) }
		<p id='errors'>{$errors}</p>
	{/if}

<br />

<div id='register'>
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

		<hr />

			<!-- <p class="cc-license">{t escape=no}Please note: we plan make your <a href="http://turtle.libre.fm/data/">listening data available</a>, under the <a href="http://wiki.openstreetmap.org/wiki/Open_Database_License">the Open Database License</a>.{/t}</p> -->

			<input type='submit' name='register' value="{t}Sign up{/t}" />


	</form>

	<p class="disclaimer">{t}We won't sell, swap or give away your email address. You can optionally include personal data on your profile, which is displayed publicly.{/t}</p>
	
</div>

{/if}
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
