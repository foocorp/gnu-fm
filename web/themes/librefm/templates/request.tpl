{include file='header.tpl'}

<h2>Request invite</h2>

{if isset($errors)}
	<p id='errors'>{$errors}</p>
{/if}

{if isset($reg)}
	<p>Your request for an invitation has been registered. Thank you for your interest in libre.fm!</p>
{else}

<div id='invite'>

	<form action='' method='post'>
		<fieldset>
	        <p><a href='http://libre.fm' rel='bookmark' class='vcard fn org url'>libre.fm</a> has unfortunately moved to a
		closed alpha, but type in your email address and you'll recieve an invitation to join as soon as possible!</p>

			<label for='email'>Email <span>must be valid!</span></label>
			<input id='email' name='email' type='text' value='' />

			<input type='submit' name='request' value='Request invite!'/>
		</fieldset>

	</form>
</div>

{/if}

{include file='footer.tpl'}
