{include file='header.tpl'}
{if $flattr_uid}
{include file='flattr.tpl'}
{/if}

<div about="{$track->id|escape:'html':'UTF-8'}" typeof="mo:Track" class="haudio">

	{if $track->streamable}
	<div id='player-container'>
	{include file='player.tpl' playlist='track'}
	</div>
	{/if}

	{include file='flattr-track-button.tpl'}

	<dl>
		<dt>{t}Artist:{/t}</dt>
		<dd rel="foaf:maker" rev="foaf:made" class="contributor vcard">
			<a about="{$artist->id|escape:'html':'UTF-8'}" typeof="mo:MusicArtist" property="foaf:name" class="url fn org"
				rel="foaf:page" rev="foaf:primaryTopic" href="{$artist->getURL()|escape:'html':'UTF-8'}">{$artist->name|escape:'html':'UTF-8'}</a>
		</dd>
		{if $album}
		<dt>{t}Album:{/t}</dt>
		<dd rev="mo:track">
			<a about="{$album->id|escape:'html':'UTF-8'}" typeof="mo:Record" property="dc:title" class="album"
				rel="foaf:page" rev="foaf:primaryTopic" href="{$album->getURL()|escape:'html':'UTF-8'}">{$album->name|escape:'html':'UTF-8'}</a>
		{/if}
		</dd>
		{if $track->mbid != ""}
		<dt>MusicBrainz ID:</dt>
		<dd>
			<a rel="mo:musicbrainz" rev="foaf:primaryTopic" href="http://musicbrainz.org/track/{$track->mbid}.html"
				class="url">{$track->mbid}</a>
		</dd>
		{/if}
	</dl>

	{if $track->licenseurl && $track->license}
	<p id='license'><a rel=":license" href="{$track->licenseurl}"><img src="{$img_url}/licenses/{$track->license}.png" /></a></p>
	{/if}
	
	<ul>
		{if !empty($track->duration)}<li property="mo:durationXSD" datatype="xsd:duration" content="PT{$track->duration}S">Duration: {$duration}</li>{/if}
		<li property="rdfs:comment">{t}Playcount:{/t} {$track->getPlayCount()}</li>
		<li property="rdfs:comment">{t}Listeners:{/t} {$track->getListenerCount()}</li>
	</ul>
	{if $track->streamable}
	{if $track->downloadurl}
	<p style='padding-left: 1em;'><b><a href='{$track->downloadurl}'>{t}Download track{/t}</a></b></p>
	{elseif $track->streamurl}
	<p style='padding-left: 1em;'><b><a href='{$track->streamurl}'>{t}Download track{/t}</a></b></p>
	{/if}
	{/if}

	{if $logged_in}
		{if $isloved}
				<form action='' method='post'>
					<input type='submit' name='unlove' id='unlove' value='{t}Unlove this track{/t}' />
				</form>
		{else}
			<form action='' method='post'>
				<input type='submit' name='love' id='love' value='{t}Love this track{/t}' />
				</form>
		{/if}
	{/if}
	
	{if !empty($tagcloud)}
		<h3 style='text-align: center; clear: left;'>{t}Tags used to describe this track{/t}</h3>
		<ul class="tagcloud">
		{section name=i loop=$tagcloud}
			<li style='font-size:{$tagcloud[i].size}'><a href='{$tagcloud[i].pageurl}' title='{t uses=$tagcloud[i].count}This tag was used %1 times{/t}' rel='tag'>{$tagcloud[i].name}</a></li>
		{/section}
		</ul>
	{/if}
	<br />

</div>
{include file='footer.tpl'}
