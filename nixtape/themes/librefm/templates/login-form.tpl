<table>
<tr>
<td>
<label for='username'>{t}Username{/t}<span>&nbsp;</span></label>
<input id='username' name='username' type='text' value='{$username}' maxlength='64' />
</td>
<td>
<label for='password'>{t}Password{/t}<span>&nbsp;</span></label>
<input id='password' name='password' type='password' value=''/>
<input type='submit' name='login' value='{t}Login{/t}' />
<input name="return" type="hidden" value="{$return|htmlentities}" />
</td>
</tr>
</table>