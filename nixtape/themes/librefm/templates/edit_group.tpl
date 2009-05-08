{include file='header.tpl'}

{if $newform}

<h2 property="dc:title">{t}Create a group{/t}</h2>

<form action="{$base_url}/edit_group.php" method="post">

<p style="display:center">
<label for="new">{t}Address for the group:{/t}</label><br />
<b>{$base_url}/group/</b><input id="new" name="new" size="12" /></p>

<p>Please remember that Libre.fm is focused on freedom, so try to
create groups that can help that goal. Also be mindful of the power of
words and the importance of framing the issues we discuss and work on
in the best possible terms.</p>

<p>
<input name="group" value="new" type="hidden" />
<input type="submit" value=" {t}Create{/t} " />
</p>

</form>

{else}

<h2 property="dc:title">{t}Edit your group{/t}</h2>

<p><strong>{t}The form below is still very experimental. Using this may wreck your account!{/t}</strong></p>

<form action="{$base_url}/edit_group.php" method="post" class="notcrazy">
	<table>
		<tr>
			<th align="right" valign="top"><label for="fullname">{t}Full name:{/t}</label></th>
			<td><input name="fullname" id="fullname" value="{$fullname|escape:'html':'UTF-8'}" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="homepage">{t}Homepage URL:{/t}</label></th>
			<td><input name="homepage" id="homepage" value="{$homepage|escape:'html':'UTF-8'}" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="avatar_uri">{t}Logo URL:{/t}</label></th>
			<td><input name="avatar_uri" id="avatar_uri" value="{$avatar_uri|escape:'html':'UTF-8'}" /></td>
			<td><a href="#dfn_avatar_uri" rel="glossary">{t}What's this?{/t}</a></td>
		</tr>
		<tr>
			<th align="right" valign="top"><label for="bio">{t}Description:{/t}</label></th>
			<td><textarea name="bio" id="bio" rows="6" cols="30" style="width:100%;min-width:20em">{$bio|escape:'html':'UTF-8'}</textarea></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="3" align="center">
				<input type="submit" value="Change" />
				<input name="submit" value="1" type="hidden" />
				<input name="group" value="{$group|escape:'html':'UTF-8'}" type="hidden" />
			</td>
		</tr>
	</table>
</form>

<h3>Help</h3>
<dl>
	<dt id="dfn_avatar_uri">Logo URL</dt>
	<dd>{t}The web address for a picture to represent your group on libre.fm. It should
	not be more than 80x80 pixels. (64x64 is best.){/t}</dd>
</dl>

{/if}


{include file='footer.tpl'}
