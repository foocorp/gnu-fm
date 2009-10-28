<div><label for='username'>{t}Username{/t}<span>&nbsp;</span></label>
<input id='username' name='username' type='text' value='{$username}' maxlength='64' /></div>
<div><label for='password'>{t}Password{/t}<span>&nbsp;</span></label>
<input id='password' name='password' type='password' value=''/></div>
<div><input type='submit' name='login' value='{t}Login{/t}' />
<input name="return" type="hidden" value="{$return|htmlentities}" /></div>
