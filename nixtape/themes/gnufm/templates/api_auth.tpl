<html>
	<body>
		<h2>{$site_name}</h2>
		{if $error_msg}
			<p>{$error_msg}</p>
		{elseif $stage == 'deskapp2.2'}
			<p>Thank you very much {$username}. Your authorization has been recorded.</p>
			<p>You may now close this page.</p>
		{else}
			<p>Uknown client with<br />
			{if $stage == 'webapp1'}
				URL: <b>{$cb}</b><br />
			{/if}
				API key: <b>{$api_key}</b><br />
				wants your permission to talk with this service.</p>
			<form method="post" action="">
				<p>Your Username: <input type="text" name="username" /></p>
				<p>Your Password: <input type="password" name="password" /></p>
				<p>
				<input type="submit" value="Submit" />
				<input type="hidden" name="api_key" value="{$api_key}" />
				<input type="hidden" name="token" value="{$token}" />
				{if $cb}
					<input type="hidden" name="cb" value="{$cb}" />
				{/if}
				</p>
			</form>
		{/if}
	</body>
</html>
