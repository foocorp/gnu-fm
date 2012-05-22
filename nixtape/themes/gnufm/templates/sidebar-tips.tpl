<div class='sideblock'>
	<h3>{t}Did you know...{/t}</h3>

	<p>
	{math equation='rand(0, 0)' assign=tip}

	{if $tip == 0}
		{t site=$site_name escape=no}You can listen to a selection of the %1 community's favourite tracks on our <a href='{$base_url}/listen.php?station=librefm://community/loved'>Community Station</a>. It's made up out of a random selection of all the tracks that %1 users have said they loved, with the most popular ones being played most frequently.{/t}
	{/if}
	</p>

</div>
