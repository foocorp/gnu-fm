{include file='header.tpl'}

<div about="{$track->id|escape:'html':'UTF-8'}" typeof="mo:Track" class="haudio">
	{if $isloved}
		<form action='' method='post'>
			<input type='submit' name='unlove' id='unlove' value='{t}Unlove this track{/t}' />
		</form>
	{else}
		<form action='' method='post'>
			<input type='submit' name='love' id='love' value='{t}Love this track{/t}' />
		</form>
	{/if}
</div>

{include file='footer.tpl'}
