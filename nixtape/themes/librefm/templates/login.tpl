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

<div class="yui-g">
    <div class="yui-u first" id="login-form">

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

    <div class="yui-u" id="privacy">

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

{include file='footer.tpl'}
