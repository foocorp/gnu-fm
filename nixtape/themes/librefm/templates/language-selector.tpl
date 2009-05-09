<form method='get' action=''>
	<select name='lang' id='lang' onchange='this.form.submit()'>
		<option {if $current_lang.en_US}selected="selected"{/if} value='en_US'>English (US)</option>
		<option {if $current_lang.de_DE}selected="selected"{/if} value='de_DE'>Deutsch (Deutschland)</option>
	</select>
	<noscript>
		<input type='submit' value='{t}Change Language{/t}' />
	</noscript>
</form>
