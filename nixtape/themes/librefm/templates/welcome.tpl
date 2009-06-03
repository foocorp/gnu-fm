{include file='header.tpl'}
{if ($logged_in)}
<h2>Welcome, <a href="{$this_user->getURL()}">{$this_user->name}</a>.</h2>
{else}
<h2>A better deal for artists and users.</h2>
{/if}
<div class="yui-gc"> 
    <div class="yui-u first">
    </div>
    <div class="yui-u">   
    {include file='menu.tpl'}
    </div>
</div>

{include file='footer.tpl'}
