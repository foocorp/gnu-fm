{if $track->streamurl}
<div id="player">
	<audio id="audio" src="{$track->streamurl}">
		<object style="width:200px;height:50px;" type="application/ogg" data="{$track->streamurl}"><a type="application/ogg" rel="enclosure" href="{$track->streamurl}">Listen to this track</a></object>
	</audio>
	<div id="player-interface">
		<div id="playbutton">
			<a href="#" onclick="play()">Listen to this track</a>
		</div>
	</div>
</div>
{/if}
