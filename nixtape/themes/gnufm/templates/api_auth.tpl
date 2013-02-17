{include file='mini-header.tpl'}
		{if $error_msg}
			<p>{$error_msg}</p>
		{elseif $stage == 'deskapp2.2'}
			<p>Thank you very much {$username}. Your authorization has been recorded.</p>
			<p>You may now close this page.</p>
		{else}
			{if $username}<h3>Hello {$username}</h3>{/if}
			{if $clientname == 'Unknown client'}
			<p><a href="{$clienturl}">{$clientname}</a> with<br /> API key: <b>{$api_key}</b><br />
			{if $cb}
				Callback URL: <b>{$cb}</b><br />
			{/if}
			{else}
			<p><a href="{$clienturl}">{$clientname}</a>
			{/if}
				wants your permission to talk with this service.</p>
			<form method="post" action="">
				{if !$logged_in}
				<p>Your Username: <input type="text" name="username" /></p>
				<p>Your Password: <input type="password" name="password" /></p>
				<p>
				{/if}
				<input type="submit" value="Submit" />
				<input type="hidden" name="api_key" value="{$api_key}" />
				<input type="hidden" name="token" value="{$token}" />
				{if $cb}
					<input type="hidden" name="cb" value="{$cb}" />
				{/if}
				</p>
			</form>
		{/if}
{include file='mini-footer.tpl'}
