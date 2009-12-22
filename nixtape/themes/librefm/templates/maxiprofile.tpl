<div about="{$me->id|escape:'html':'UTF-8'}" typeof="foaf:Agent" class="user vcard">

	{if $me->fullname}
	<h2>{$me->fullname|escape:'html':'UTF-8'}</h2>
	{else}
	<h2>{$me->name|escape:'html':'UTF-8'}</h2>	
	{/if}

	{if $isme}
	<p><a href="{$me->getURL('edit')|escape:'html':'UTF-8'}"><b>Edit my profile</b></a></p>
	{/if}

	<div class="avatar" rel="foaf:depiction">
		<p><img src="{$me->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" /></p>
	</div>

	<ul>
		{if $me->homepage}
		<li>
			<a href="{$me->homepage|escape:'html':'UTF-8'}" rel="me foaf:homepage" class="url">{$me->homepage|escape:'html':'UTF-8'}</a>
		</li>
		{/if}
		{if $me->laconica_profile}
		<li>
			<a href="{$me->laconica_profile|escape:'html':'UTF-8'}" rel="foaf:homepage" class="url">{$me->laconica_profile|escape:'html':'UTF-8'} (microblog)</a>
		</li>
		{/if}
		{if $me->location}
		<li rel="foaf:based_near">
			<span {if $me->location_uri} about="{$me->location_uri|escape:'html':'UTF-8'}"{/if}>
				<span class="label" property="rdfs:comment">{$me->location|escape:'html':'UTF-8'}</span>
				{if $geo.latitude}
				<small class="geo">
					[<span class="latitude" property="geo:lat">{$geo.latitude|string_format:"%0.3f"}</span>;
					<span class="longitude" property="geo:long">{$geo.longitude|string_format:"%0.3f"}</span>]
				</small>
				{/if}
				{if $geo.country}
				<small xmlns:gn="http://www.geonames.org/" rel="gn:ontology#inCountry" resource="[gn:countries/#{$geo.country}]">
					(<a rel="foaf:page" href="{$base_url}/country/{$geo.country}">Find other people in {$geo.country_name|escape:'html':'UTF-8'}</a>)
				</small>
				{/if}
			</span>
		</li>
		{/if}
		{if $me->bio}
		<li class="note" property="bio:olb">{$me->bio|escape:'html':'UTF-8'}</li>
		{/if}
	</ul>

	<ul>
		<li><a{if $this_page_absolute != $me->getURL()} rel="rdfs:seeAlso" href="{$me->getURL()|escape:'html':'UTF-8'}"{/if}>profile</a></li>
		<li><a{if $this_page_absolute != $me->getURL('stats')} rel="rdfs:seeAlso" href="{$me->getURL('stats')|escape:'html':'UTF-8'}"{/if}>stats</a></li>
		<li><a{if $this_page_absolute != $me->getURL('recent-tracks')} rel="rdfs:seeAlso" href="{$me->getURL('recent-tracks')|escape:'html':'UTF-8'}"{/if}>recent tracks</a></li>
		{if $me->journal_rss} <li><a{if $this_page_absolute != $me->getURL('journal')} rel="rdfs:seeAlso" href="{$me->getURL('journal')|escape:'html':'UTF-8'}"{/if}>journal</a></li>{/if}
		<li><a{if $this_page_absolute != $me->getURL('groups')} rel="rdfs:seeAlso" href="{$me->getURL('groups')|escape:'html':'UTF-8'}"{/if}>groups</a></li>
	</ul>
	<hr />
