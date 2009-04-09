<div id="menu">
	{if isset($logged_in)}
  <p>Logged in as: {$username}</p>
  {/if}

  <ul id="navigation">
  {if isset($logged_in)}
  		<li>Invites disabled</li>
	{else}
  	<li><a href="{$base_url}/login.php">Login</a></li>
	  <li><a href="{$base_url}/register.php">Register</a></li>
	{/if}
    <li>Explore:
      <ul>
        <li><a href="explore.php?mode=artists">Artists</a></li>
      </ul>
    </li>
  </ul>
</div>
