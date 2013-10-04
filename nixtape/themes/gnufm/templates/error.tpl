{include file='header.tpl'}

{if $default_theme != 'librefm'} {* librefm theme compat, may be removed after switch to BS3 theme *}
<h1>{$pagetitle}</h1>
{/if}
{$error_message|escape:'htmlall'}

{include file='footer.tpl'}
