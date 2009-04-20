{include file='header.tpl'}

<h2 property="dc:title">Edit your profile</h2>

<script type="text/javascript" src="{$base_url}/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="{$base_url}/js/edit_profile.js"></script>
<form action="{$base_url}/edit_profile.php" method="post">
	<table>
		<tr>
			<th align="right" valign="top"><label for="fullname">Full name:</label></th>
			<td><input name="fullname" id="fullname" value="{$fullname|htmlentities}" /></td>
		</tr>
		<tr>
			<th align="right" valign="top" rowspan="2"><label for="location">Location:</label></th>
			<td><input name="location" id="location" value="{$location|htmlentities}" /></td>
		</tr>
		<tr>
			<td id="chooser">
				<input type="hidden" name="location_uri" id="location_uri" value="{$location_uri|htmlentities}" />
				<input type="button" value="Check ..." onclick="LocationCheck();" />
				<span id="location_uri_label"></span>
			</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="homepage">Homepage URL:</label></th>
			<td><input name="homepage" id="homepage" value="{$homepage|htmlentities}" /></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="avatar_uri">Avatar URL:</label></th>
			<td><input name="avatar_uri" id="avatar_uri" value="{$avatar_uri|htmlentities}" /></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="bio">Mini Biography:</label></th>
			<td><textarea name="bio" id="bio" rows="6" cols="30" style="width:100%;min-width:20em">{$bio|htmlentities}</textarea></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="id">WebID (FOAF):</label></th>
			<td><input name="id" id="id" value="{$id|htmlentities}" /></td>
		</tr>
		<tr>
			<td colspan="2" align="center">
				<input type="submit" value="Change" />
				<input name="submit" value="1" type="hidden" />
			</td>
		</tr>
	</table>
</form>

{include file='footer.tpl'}
