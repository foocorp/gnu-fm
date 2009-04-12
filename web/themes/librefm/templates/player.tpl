{if $track->streamurl}

<div id="player">
	<audio id="audio" src="{$track->streamurl}"></audio>
	<div id="playbutton">
		<a href="#" onclick="play()">Listen to this track</a>
	</div>
</div>
{/if}
