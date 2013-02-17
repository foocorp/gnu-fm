<html>
	<body>
		{if $error_msg}
			<p>{$error_msg}</p>
		{elseif $stage == 'deskapp2.2'}
			<p>Thanks you very much {$username}. Your authorization has been recorded.</p>
			<p>You may now close this page.</p>
		{else}
			{if $stage == 'webapp1'}
				<p>webapp with callback {$cb} and api key {$api_key}</p>
			{elseif $stage == 'deskapp1'}
				<p>deskapp with api key {$api_key}</p>
			{/if}
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
