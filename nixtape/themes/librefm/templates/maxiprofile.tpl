<div about="{$me->id|escape:'html':'UTF-8'}" typeof="foaf:Agent" class="user vcard">

	<div class="avatar" rel="foaf:depiction">
		<img src="{$me->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" />
	</div>

	{if $isme}
	<a class="edit" href="{$me->getURL('edit')|escape:'html':'UTF-8'}">[edit]</a>
	{/if}

	<dl>
		<dt>
			<span class="fn" property="foaf:name">{$me->fullname|escape:'html':'UTF-8'}</span>
			<span property="foaf:nick" content="{$me->name|escape:'html':'UTF-8'}" rel="foaf:holdsAccount" rev="sioc:account_of">
				<span about="{$me->acctid|escape:'html':'UTF-8'}" typeof="sioc:User">
					(<span class="nickname" property="foaf:accountName">{$me->name|escape:'html':'UTF-8'}</span>)
					<span rel="foaf:accountServiceHomepage" resource="{$base_url}"></span>
					<span rel="foaf:accountProfilePage" rev="foaf:topic" resource="{$me->getURL()|escape:'html':'UTF-8'}"></span>
				</span>
			</span>
		</dt>
		{if $me->homepage}
		<dd>
			<a href="{$me->homepage|escape:'html':'UTF-8'}" rel="me foaf:homepage" class="url">{$me->homepage|escape:'html':'UTF-8'}</a>
		</dd>
		{/if}
		{if $me->laconica_profile}
		<dd>
			<a href="{$me->laconica_profile|escape:'html':'UTF-8'}" rel="foaf:homepage" class="url">{$me->laconica_profile|escape:'html':'UTF-8'} (microblog)</a>
		</dd>
		{/if}
		{if $me->location}
		<dd rel="foaf:based_near">
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
		</dd>
		{/if}
		{if $me->bio}
		<dd class="note" property="bio:olb">{$me->bio|escape:'html':'UTF-8'}</dd>
		{/if}
	</dl>

	<div style="text-align:right;clear:right;font-size:80%">
		<a{if $this_page_absolute != $me->getURL()} rel="rdfs:seeAlso" href="{$me->getURL()|escape:'html':'UTF-8'}"{/if}>profile</a>
		&middot; <a{if $this_page_absolute != $me->getURL('stats')} rel="rdfs:seeAlso" href="{$me->getURL('stats')|escape:'html':'UTF-8'}"{/if}>stats</a>
		&middot; <a{if $this_page_absolute != $me->getURL('recent-tracks')} rel="rdfs:seeAlso" href="{$me->getURL('recent-tracks')|escape:'html':'UTF-8'}"{/if}>recent tracks</a>
		{if $me->journal_rss} &middot; <a{if $this_page_absolute != $me->getURL('journal')} rel="rdfs:seeAlso" href="{$me->getURL('journal')|escape:'html':'UTF-8'}"{/if}>journal</a>{/if}
		&middot; <a{if $this_page_absolute != $me->getURL('groups')} rel="rdfs:seeAlso" href="{$me->getURL('groups')|escape:'html':'UTF-8'}"{/if}>groups</a>
	</div>
	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:page" rev="foaf:primaryTopic" resource="" />

</div>
