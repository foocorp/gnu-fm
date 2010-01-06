<table>
<tr>
<td>
<label for='username'>{t}Username:{/t}</label></td>
<td>			<input id='username' name='username' type='text' value='{$username}' maxlength='16' autocomplete="off" /></td>
</tr>
<tr>
<td><label for='email'>{t}Your e-mail:{/t}<span>{t}(must be valid!){/t}</span></label></td>
<td><input id='email' name='email' type='text' value='{$email}' maxlength='64' autocomplete='off' /></td>
</tr>
<tr>
<td><label for='password'>{t}New password:{/t}</label></td>
<td><input id='password' name='password' type='password' value='' autocomplete="off"/></td>
</tr>
<tr>
<td><label for='password-repeat'>{t}Password again:{/t}</label></td>
<td><input id='password-repeat' name='password-repeat' type='password' autocomplete="off" value=''/></td>
</tr>
</table>

<p>{t}We won't sell, swap or give away your email address. You can optionally include personal data on your profile, which is displayed publicly.{/t}</p>

<p><input type='submit' name='register' value="{t}Sign up{/t}" /></p>
