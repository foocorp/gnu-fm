{include file='header.tpl'}

<h2>Invite</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($sent)}
	<p>{t}Your invitation has been sent, pretty soon your friend should be thanking you profusely.{/t}</p>
{else}

<div id='invite'>

	<form action='' method='post'>
		<fieldset>
	        <p>{t escape=no}Do you have an awesome friend you'd like to share with? Just enter his/her email address and we'll sort them out with an invitation code.{/t}</p>

			<label for='email'>{t}Invitee's E-mail:{/t}<span>{t}must be valid!{/t}</span></label>
			<input id='email' name='email' type='text' value='' />

			<input type='submit' name='invite' value='{t}Let them in!{/t}'/>
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
