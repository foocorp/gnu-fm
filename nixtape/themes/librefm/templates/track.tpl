{include file='header.tpl'}

<div about="{$track->id|escape:'html':'UTF-8'}" typeof="mo:Track" class="haudio">

	<h2 property="dc:title" class="fn" rel="foaf:page" rev="foaf:primaryTopic" resource="">{$track->name|escape:'html':'UTF-8'}</h2>

	<dl>
		<dt>{t}Artist:{/t}</dt>
		<dd rel="foaf:maker" rev="foaf:made" class="contributor vcard">
			<a about="{$artist->id|escape:'html':'UTF-8'}" typeof="mo:MusicArtist" property="foaf:name" class="url fn org"
				rel="foaf:page" rev="foaf:primaryTopic" href="{$artist->getURL()|escape:'html':'UTF-8'}">{$artist->name|escape:'html':'UTF-8'}</a>
		</dd>
		<dt>{t}Album:{/t}</dt>
		<dd rev="mo:track">
			<a about="{$album->id|escape:'html':'UTF-8'}" typeof="mo:Record" property="dc:title" class="album"
				rel="foaf:page" rev="foaf:primaryTopic" href="{$album->getURL()|escape:'html':'UTF-8'}">{$album->name|escape:'html':'UTF-8'}</a>
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
	<p><a rel=":license" href="{$track->licenseurl}"><img src="{$base_url}/themes/librefm/images/licenses/{$track->license}.png" /></a></p>
	{/if}

	<ul>
		{if !empty($track->duration)}<li property="mo:durationXSD" datatype="xsd:duration" content="PT{$track->duration}S">Duration: {$track->duration}</li>{/if}
		<li property="rdfs:comment">{t}Playcount:{/t} {$track->getPlayCount()}</li>
		<li property="rdfs:comment">{t}Listeners:{/t} {$track->getListenerCount()}</li>
	</ul>
  
	{include file='player.tpl'}
	<script type="text/javascript">
		var playlist = [{ldelim}"artist" : "{$track->artist_name}", "album" : "{$track->album_name}", "track" : "{$track->name}", "url" : "{$track->streamurl}"{rdelim}];
		{if isset($this_user)}
		playerInit(playlist, "{$this_user->getScrobbleSession()}", false);
		{else}
		playerInit(playlist, false, false);
		{/if}
	</script>

 
</div>
{include file='footer.tpl'}
