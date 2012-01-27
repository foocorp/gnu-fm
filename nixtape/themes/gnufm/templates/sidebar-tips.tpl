<div class='sideblock'>
	<h3>{t}Did you know...{/t}</h3>

	<p>
	{math equation='rand(0, 3)' assign=tip}

	{if $tip == 0}
		{t escape=no}You can listen to a selection of the Libre.fm community's favourite tracks on our <a href='{$base_url}/listen.php?station=librefm://community/loved'>Community Station</a>. It's made up out of a random selection of all the tracks that Libre.fm users have said they loved, with the most popular ones being played most frequently.{/t}
	{elseif $tip == 1}
		{t escape=no}You can now get a monthly summary of the latest goings on in the Libre.fm world on our <A href='http://libre.fm/news/'>news site</a>.{/t}
	{elseif $tip == 2}
		{t escape=no}Libre.fm has a monthly <a href='http://libre.fm/podcast'>podcast</a>, featuring a selection of some of the best music on Libre.fm, a summary of recent development news and the occasional interview.{/t}
	{elseif $tip == 3}
		{t escape=no}If your client only supports scrobbling to a single host but you'd like to scrobble to both Libre.fm and Last.fm you can configure Libre.fm to forward all of your scrobbles on to Last.fm for you. Simply visit the <a href='{$base_url}/user-connections.php'>connections settings</a> on your profile editing page.{/t}
	{/if}
	</p>

</div>
