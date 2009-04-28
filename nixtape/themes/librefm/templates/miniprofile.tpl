<div about="{$me->id|escape:'html':'UTF-8'}" typeof="foaf:Agent" class="user vcard">

	<div class="avatar" rel="foaf:depiction">
		<!-- Avatar placeholder  -->
		<img src="{$me->getAvatar()|escape:'html':'UTF-8'}" alt="avatar" class="photo" width="64" height="64" />
	</div>
	
	<dl>
		<dt>
			<span class="fn" property="foaf:name">{$me->fullname|escape:'html':'UTF-8'}</span>
			<span rel="foaf:holdsAccount" rev="sioc:account_of">
				<span about="{$me->acctid|escape:'html':'UTF-8'}" typeof="sioc:User">
					(<a class="nickname" property="foaf:accountName" href="{$base_url}/user/{$me->name}" rel="foaf:accountProfilePage" rev="foaf:topic">{$me->name|escape:'html':'UTF-8'}</a>)
					<span rel="foaf:accountServiceHomepage" resource="{$base_url}"></span>
				</span>
			</span>
		</dt>
		{if $me->homepage}
		<dd>
			<a href="{$homepage|escape:'html':'UTF-8'}" rel="me foaf:homepage" class="url">{$me->homepage|escape:'html':'UTF-8'}</a>
		</dd>
		{/if}
		<dd rel="foaf:based_near">
			<span {if $me->location_uri} about="{$me->location_uri|escape:'html':'UTF-8'}"{/if}>
				<span class="label" property="rdfs:comment">{$me->location|escape:'html':'UTF-8'}</span>
			</span>
		</dd>
		<dd class="note" property="bio:olb">{$me->bio|escape:'html':'UTF-8'}</dd>
	</dl>

	<hr style="border: 1px solid transparent; clear: both;" rel="foaf:page" rev="foaf:primaryTopic" resource="{$base_url}/user/{$me->name}" />

</div>
