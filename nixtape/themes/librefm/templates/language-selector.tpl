<form method='get' action=''>
	<select name='lang' id='lang' onchange='this.form.submit()'>
		<optgroup label="Please select your language">
		<option {if $current_lang.en_US}selected="selected"{/if} value='en_US'>English (US)</option>
		<option {if $current_lang.de_DE}selected="selected"{/if} value='de_DE'>Deutsch (Deutschland)</option>
		</optgroup>
	</select>
	<noscript>
		<input type='submit' value='{t}Change Language{/t}' />
	</noscript>
</form>
