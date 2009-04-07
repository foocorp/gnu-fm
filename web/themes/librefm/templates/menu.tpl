<div id="menu">
	{if isset($logged_in)}
		<a href="{$base_url}/invite.php">Invite a friend</a><br />
	{else}
		<a href="{$base_url}/login.php">Login</a><br />
		<a href="{$base_url}/register.php">Register</a><br />
	{/if}
</div>
