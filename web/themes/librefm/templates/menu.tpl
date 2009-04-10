<div id="menu">
	{if ($logged_in)}
  <p>Logged in as: <a href="{$base_url}/profile.php?user={$username|urlencode}">{$username}</a></p>
  {/if}

  <ul id="navigation">
  {if ($logged_in)}
  		<li>Invites disabled</li>
		{if $userlevel > 0}
		    <li><a href="/admin.php">Admin panel</a></li>
		{/if}
	{else}
  	<li><a href="{$base_url}/login.php">Login</a></li>
	  <li><a href="{$base_url}/request.php">Request invitation</a></li>
	{/if}
    <li>Explore:
      <ul>
        <li><a href="explore.php?mode=artists">Artists</a></li>
      </ul>
    </li>
  </ul>
</div>
