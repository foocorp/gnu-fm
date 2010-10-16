{include file='mini-header.tpl'}

<h2>{t}Reset my password{/t}</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($changed)}
	<p>{t}Your new password has been emailed to you.{/t}</p>
{elseif isset($sent)}
	<p>{t}An email with further information has been sent to the email address associated with your profile.{/t}</p>
{else}
<div id='invite'>

<p>Enter your email address and we'll email you a link to reset your
password. All passwords are encrypted in our database.</p>

	<form action="{$base_url}/reset.php" method='post'>
		<fieldset>

			<h3><label for='username'>{t}Username{/t}</span></label></h3>
			<div><input id='username' name='user' type='text' value='' /></div><br/>
			or:<br/>
			<div><input id='email' name='email' type='text' value='' />
			<p><input type='submit' name='recover' value='{t}Reset my password!{/t}'/></p>
		</fieldset>

	</form>
</div>

{/if}

{include file='mini-footer.tpl'}
