{include file='header.tpl'}

<h2>Register</h2>

{if isset($activated)}
	<h2>Your account has been activated! You may now login!</h2>

{elseif isset($registered) }
	<h2>You're now registered with libre.fm! An email has been sent to the email address you
	provided. Please follow the link in the email to activate your account!</h2>
	
	<ul>
	<li><a href="http://ideas.libre.fm/index.php/Using_turtle">Find out how to start sending us your listening habits</a></li>
	<li><a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">Join our mailing list</a></li>
	<li><a href="http://blog.libre.fm/">Read our blog</a> and <a href="http://identi.ca/mattl">subscribe to Matt (our founder) on identi.ca</a></li>
	</ul>


{else}

	{if isset($errors) }
		<p id='errors'>{$errors}</p>
	{/if}

<br />

<div id='register'>
	<form action='' method='post'>
		<fieldset>

			<label for='username'>Your username:<span>(no more than	16 chars.)</span></label>
			<input id='username' name='username' type='text' value='{$username}' maxlength='16' />

			<label for='password'>Your password:<span>(make it hard to guess)</span></label>
			<input id='password' name='password' type='password' value=''/>

			<label for='password-repeat'>Your password again<span>(you should repeat it.)</span></label>
			<input id='password-repeat' name='password-repeat' type='password' value=''/>

			<label for='email'>Your e-mail:<span>(must be valid!)</span></label>
			<input id='email' name='email' type='text' value='{$email}' maxlength='64' />

			</fieldset>

			<fieldset class="optional"><legend>Optional profile information</legend>

			<label for='fullname'>Name:</label>
			<input id='fullname' name='fullname' type='text' value='{$fullname}' maxlength='255'/>
	
			<label for='location'>Location:<span>(like 'CABA, Buenos Aires, Argentina')</span></label>
			<input id='location' name='location' type='text' value='{$location}' maxlength='255' />
			
			<label for='bio'>About yourself:<span>(we want to know you! in 140 chars.)</span></label>
			<input id='bio' name='bio' type='text' value='{$bio}' maxlength='140'/>

			<input type='submit' name='register' value="OK, I'm in" />
		</fieldset>

	</form>
</div>

{/if}
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
