<div about="{$id|escape:'html':'UTF-8'}" typeof="foaf:Agent" class="user vcard">

	<div class="avatar" rel="foaf:depiction">
		<img src="{$avatar|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" />
	</div>

	{if $isme}
	<a class="edit" href="{$base_url}/edit_profile.php">[edit]</a>
	{/if}

	<dl>
		<dt>
			<span class="fn" property="foaf:name">{$fullname|escape:'html':'UTF-8'}</span>
			<span rel="foaf:holdsAccount" rev="sioc:account_of">
				<span about="{$acctid|escape:'html':'UTF-8'}" typeof="sioc:User">
					(<span class="nickname" property="foaf:accountName">{$user|escape:'html':'UTF-8'}</span>)
					<span rel="foaf:accountServiceHomepage" resource="{$base_url}"></span>
					<span rel="foaf:accountProfilePage" rev="foaf:topic" resource="{$base_url}/user/{$name}"></span>
				</span>
			</span>
		</dt>
		{if $homepage}
		<dd>
			<a href="{$homepage|escape:'html':'UTF-8'}" rel="me foaf:homepage" class="url">{$homepage|escape:'html':'UTF-8'}</a>
		</dd>
		{/if}
		<dd rel="foaf:based_near">
			<span {if $location_uri} about="{$location_uri|escape:'html':'UTF-8'}"{/if}>
				<span class="label" property="rdfs:comment">{$location|escape:'html':'UTF-8'}</span>
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
		<dd class="note" property="bio:olb">{$bio|escape:'html':'UTF-8'}</dd>
	</dl>

	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:page" rev="foaf:primaryTopic" resource="" />

</div>
