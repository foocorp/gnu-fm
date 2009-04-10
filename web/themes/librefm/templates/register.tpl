{include file='header.tpl'}

<h2>Register</h2>

{if !isset($authcode) }
	<p>Sorry! You need to have an invite to be able to register.</p>
{elseif $invalid_authcode }
	<p>Sorry! That invitation code is either invalid or has already been used.</p>
{elseif isset($registered) }
	<p>You're now registered with libre.fm! Yay!</p>
	<p><small>(p.s. we love you)</small></p>
{else}

	{if isset($errors) }
		<p id='errors'>{$errors}</p>
	{/if}

<br />

<div id='register'>
	<form action='' method='post'>
		<fieldset>
			<label for='fullname'>You:<span>(that's your real name.)</span></label>
			<input id='fullname' name='fullname' type='text' value='{$fullname}' maxlength='255'/>
	
			<label for='username'>Your nickname:<span>(no more than 64 chars.)</span></label>
			<input id='username' name='username' type='text' value='{$username}' maxlength='64' />

			<label for='email'>Your e-mail:<span>(must be valid!)</span></label>
			<input id='email' name='email' type='text' value='{$email}' maxlength='64' />
	
			<label for='location'>Location:<span>(like 'CABA, Buenos Aires, Argentina')</span></label>
			<input id='location' name='location' type='text' value='{$location}' maxlength='255' />

			<label for='password'>Your password:<span>(make it hard to guess)</span></label>
			<input id='password' name='password' type='password' value=''/>

			<label for='password-repeat'>Your password again<span>(you should repeat it.)</span></label>
			<input id='password-repeat' name='password-repeat' type='password' value=''/>

			<label for='bio'>About yourself:<span>(we want to know you! in 140 chars.)</span></label>
			<input id='bio' name='bio' type='text' value='{$bio}' maxlength='140'/>

			<input type='submit' name='register' value="OK, I'm in" />
		</fieldset>

	</form>
</div>

{/if}
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
