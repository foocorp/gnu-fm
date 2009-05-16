{include file='header.tpl'}

<h2 property="dc:title">Edit your profile</h2>

<p><strong>{t}The form below is still very experimental. Using this may wreck your account!{/t}</strong></p>

<form action="{$base_url}/user-edit.php" method="post" class="notcrazy">
	<table>
		<tr>
			<th align="right" valign="top"><label for="fullname">{t}Full name:{/t}</label></th>
			<td><input name="fullname" id="fullname" value="{$fullname|escape:'html':'UTF-8'}" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="location">{t}Location:{/t}</label></th>
			<td><input name="location" id="location" value="{$location|escape:'html':'UTF-8'}" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="location_uri">{t}Geoname:{/t}</label></th>
			<td id="chooser">
				<input type="hidden" name="location_uri" id="location_uri" value="{$location_uri|escape:'html':'UTF-8'}" />
				<input type="button" value="{t}Find ...{/t}" onclick="LocationCheck();" />
				<span id="location_uri_label"></span>
			</td>
			<td><a href="#dfn_location_uri" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="homepage">{t}Homepage URL:{/t}</label></th>
			<td><input name="homepage" id="homepage" value="{$homepage|escape:'html':'UTF-8'}" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="avatar_uri">{t}Avatar URL:{/t}</label></th>
			<td><input name="avatar_uri" id="avatar_uri" value="{$avatar_uri|escape:'html':'UTF-8'}" /></td>
			<td><a href="#dfn_avatar_uri" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="bio">{t}Mini Biography:{/t}</label></th>
			<td><textarea name="bio" id="bio" rows="6" cols="30" style="width:100%;min-width:20em">{$bio|escape:'html':'UTF-8'}</textarea></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="id">WebID (FOAF):</label></th>
			<td>
				<input name="id" id="id" value="{$id|escape:'html':'UTF-8'}" />
				<input type="button" onclick="webidLookup();" value="find!" />
			</td>
			<td><a href="#dfn_id" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="laconica_profile">{t}Laconica/identi.ca Profile:{/t}</label></th>
			<td><input onchange="laconicaChange();" onclick="laconicaClick();" name="laconica_profile" id="laconica_profile" value="{$laconica_profile|escape:'html':'UTF-8'}" /></td>
			<td><a href="#dfn_laconica_profile" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="journal_rss">{t}RSS Feed:{/t}</label></th>
			<td><input name="journal_rss" id="journal_rss" value="{$journal_rss|escape:'html':'UTF-8'}" /></td>
			<td><a href="#dfn_journal_rss" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="anticommercial">{t}Anticommercial{/t}</label></th>
			<td><input name="anticommercial" id="anticommercial" type="checkbox"{if $anticommercial == 1} checked="checked"{/if} /></td>
			<td><a href="#dfn_anticommercial" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="password_1">{t}Password:{/t}</label></th>
			<td><input name="password_1" id="password_1" type="password" value="" /></td>
			<td rowspan="2">{t}Leave this blank if you don't want to change your password.{/t}</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="password_2">{t}Confirm Password:{/t}</label></th>
			<td><input name="password_2" id="password_2" type="password" value="" /></td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<input type="submit" value="Change" />
				<input name="submit" value="1" type="hidden" />
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript" src="{$base_url}/js/user-edit.js"></script>

<h3>Help</h3>
<dl>
	<dt id="dfn_location_uri">{t}Location check{/t}</dt>
	<dd>{t escape=no}This feature looks up your location on <a href="http://www.geonames.org">geonames</a>. You don't need to do it, but it will help us find your latitude and longitude, which will help us add some great location-based features in the future.{/t}</dd>

	<dt id="dfn_avatar_uri">{t}Avatar URL{/t}</dt>
	<dd>{t escape=no}The web address for a picture to represent you on libre.fm. It should not be more than 80x80 pixels. (64x64 is best.) If you leave this empty, libre.fm will use <a href="http://gravatar.com">Gravatar</a> to find an image for you.{/t}</dd>

	<dt id="dfn_id">WebID (FOAF)</dt>
	<dd>{t escape=no}A URI that represents you in RDF. See <a href="http://esw.w3.org/topic/WebID">WebID</a> for details. If you don't know what this is, it's best to leave it blank.{/t}</dd>

	<dt id="dfn_anticommercial">Anticommercial</dt>
	<dd>{t escape=no}By enabling this option, you will not be shown advertisements or affiliate purchase links.{/t}</dd>
</dl>

{include file='footer.tpl'}
</dl>

{include file='footer.tpl'}
