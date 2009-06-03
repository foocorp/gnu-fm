{include file='header.tpl'}

<h2>Register</h2>

{if isset($activated)}

	<h3>{t}Your account has been activated! You may now login!{/t}</h3>

{elseif isset($registered) }
	<h3>{t}You're now registered with libre.fm! An email has been sent to the email address you provided. Please follow the link in the email to activate your account!{/t}</h3>
	
	<ul>
	<li><a href="http://ideas.libre.fm/index.php/Using_turtle">{t}Find out how to start sending us your listening habits{/t}</a></li>
	<li><a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">{t}Join our mailing list{/t}</a></li>
	<li><a href="http://blog.libre.fm/">{t}Read our blog{/t}</a> {t}and{/t} <a href="http://identi.ca/mattl">{t}subscribe to Matt (our founder) on identi.ca{/t}</a></li>
	</ul>

{else}

	{if isset($errors) }
		<p id='errors'>{$errors}</p>
	{/if}

</div>

<div class="yui-g">
    <div class="yui-u first" id="privacy">
      <div>
      <h2>We take your privacy seriously</h2>

      <p>Your privacy is our primary concern. Our goal is to retain as
      little information on you as possible while still retaining the
      ability to provide reliable service and prevent abuse of server
      resources.</p>

      <p>We collect user information from you when you sign up for an
      account, specifically a username, password, and valid email address.</p>

      <p>We check your email address is valid by sending you a
      verification email address. If you are concerned by the
      possibility that your email address or username may reveal your
      identity, please use an alternative email account, or username.</p>

      <p>Most sites keep detailed records of all connections. In
      general, we do not keep information which uniquely identifies
      you to your ISP. We do not log IP addresses. For troubleshooting,
      we may enable increased logging for brief periods of time. These
      extra logs are deleted immediately after they are used.</p>
     
      <p>You can opt-in to make your listening data public.</p>

      <p>We are working on ways to allow you to delete songs you have
      previously listened to, but please bear in mind that anyone
      looking at your profile can see your most recent history.</p>

      <p>In the future, we may add functionality to allow you to hide
      your profile completely, or from users unknown to you.</p>

      </div>


</div>
    <div class="yui-u" id="register">
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
</div>

{/if}
{include file='footer.tpl'}
