{include file='mini-header.tpl'}

<h2>{t}Login{/t}</h2>

<h3>{t}Need an account?{/t} <a href="register.php">{t}Register now!{/t}</a></h3>

{if isset($errors)}
        <p id='errors'>{$errors}</p>
{/if}

<form method="post">
{include file='login-form.tpl'}
</form>

	<p>{t escape=no}Join us in #libre.fm on irc.freenode.net to help shape that, or <a href="http://lists.nongnu.org/mailman/listinfo/librefm-discuss"> join our mailing list</a> and have your say!{/t}</p>

{include file='mini-footer.tpl'}
