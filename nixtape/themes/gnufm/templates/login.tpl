{include file='mini-header.tpl'}

<h2>{t}Login{/t}</h2>

<h3>{t}Need an account?{/t} <a href="{$base_url}/register.php">{t}Register now!{/t}</a></h3>

{if isset($errors)}
        <p id='errors'>{$errors}</p>
{/if}

<form method="post">
{include file='login-form.tpl'}
</form>

{include file='mini-footer.tpl'}
