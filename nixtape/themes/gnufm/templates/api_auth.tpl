<html>
	<body>
		{if $error_msg}
			<p>{$error_msg}</p>
		{elseif $username}
			<p>Thanks you very much {$username}. Your authorization has been recorded.</p>
			<p>You may now close this page.</p>
		{else}
			<form method="post" action="">
				<p>Your Username: <input type="text" name="username" /></p>
				<p>Your Password: <input type="password" name="password" /></p>
				<p>
				<input type="submit" value="Submit" />
				<input type="hidden" name="api_key" value="{$api_key}" />
				<input type="hidden" name="token" value="{$token}" />
				</p>
			</form>
		{/if}
	</body>
</html>
