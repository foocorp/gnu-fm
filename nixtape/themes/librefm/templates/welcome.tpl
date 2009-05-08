{include file='header.tpl'}
	{if ($logged_in)}
	<!-- put something here -->
        {else}
	{if $welcome}
	<div class="yui-g" id="banner">     
	  <a href="{$base_url}/register.php"><img src="{$base_url}/i/intro1.png" alt="" /></a>
	{else}
	<div class="yui-g">     
	  <a href="{$base_url}/register.php"><img src="{$base_url}/themes/librefm/images/topblocksmall.png" alt="" /></a>
	{/if}
	</div>{/if}

<div class="yui-g">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas sit amet metus. Nunc quam elit, posuere nec, auctor in, rhoncus quis, dui. Aliquam erat volutpat. Ut dignissim, massa sit amet dignissim cursus, quam lacus feugiat.</div>
<div class="yui-gc">
    <div class="yui-u first">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas sit amet metus. Nunc quam elit, posuere nec, auctor in, rhoncus quis, dui. Aliquam erat volutpat. Ut dignissim, massa sit amet dignissim cursus, quam lacus feugiat.    </div>
    <div class="yui-u">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas sit amet metus. Nunc quam elit, posuere nec, auctor in, rhoncus quis, dui. Aliquam erat volutpat. Ut dignissim, massa sit amet dignissim cursus, quam lacus feugiat.    </div>
</div>

{include file='footer.tpl'}