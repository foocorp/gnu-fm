{include file='header.tpl'}
{if ($logged_in)}
	{if $creating}
		{if $already_exists}
			{if !empty($managers)}
				{t}We already have an artist by that name and they're already managed by:{/t}

				<ul>
				{section name=i loop=$managers}
					<li><a href='{$managers[i]->getURL()}'>{$managers[i]->name}</a></li>
				{/section}
				</ul>

				<p>{t escape=no}If you believe these users shouldn't be managing this artist please <a href='http://bugs.foocorp.net/projects/librefm/'>raise a support ticket in our bug tracker</a> and we'll check in to it.{/t}</p>
	
				<p>{t escape=no}Otherwise please <a href='{$base_url}/artist-signup.php'>try again</a> with a new artist name.{/t}</p>
			{else}
				{t escape=no artisturl=$artist->getURL()}We already have an artist by that name, but they're currently not being managed by anyone. Does <a href='%1' target='_blank'>this</a> look like you?{/t}
				<form method='post' action=''>
					<input type='hidden' value='{$artist->name}' name='artist_name' />
					<input type='submit' value="{t}Yes, that's me!{/t}" name='confirm_artist' /><br /><br />
					<input type='submit' value="{t}No, that's not me.{/t}" name='reject_artist' />
				</form>
			{/if}
		{/if}
	{elseif $created}
		{t escape=no managementurl=$artist->getManagementURL()}Awesome, you're all ready to start sharing your music! To get started head over to your <a href='%1'>artist management page</a> and start filling in a few details about yourself.{/t}
	{elseif $too_popular}
		{t escape=no}Wow, you're pretty popular! To make sure you're really associated with this band we're going to have to do some extra checks before enabling your artist account. If you're on Jamendo we'll typically send you a message there to check, otherwise we'll try to send an e-mail to the address on your band's website. Once that's all sorted we'll send you an e-mail letting you know that your account is ready for you!{/t}
	{else}
		{if $reject_artist}
			<center><p><b>{t}Sorry, in that case you'll have to pick a new name.{/t}</b></p></center>
		{/if}
		<form method='post' action=''>
			<label for="artist_name"><b>{t}Artist name:{/t}</b><br />{t}To start off just let us know the name that you like to perform under:{/t}</label><input type='text' name='artist_name' id='artist_name' /><br /><br />
			<input type="submit" value="{t}Create artist account{/t}" />
		</form>
	{/if}
{else}
	{t escape=no}Before creating an artist account you first need to <a href='{$base_url}/register.php'>register</a> or <a href='{$base_url}/login.php'>log in</a> with a normal Libre.fm account.{/t}
{/if}

{include file='footer.tpl'}
