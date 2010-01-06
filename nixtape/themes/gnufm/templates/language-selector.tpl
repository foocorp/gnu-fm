<form method='get' action=''>
	<select name='lang' id='lang' onchange='this.form.submit()'>
		<optgroup label="Please select your language">
		<option {if $lang_selector_array.en_US}selected="selected"{/if} value='en_US'>English (US)</option>
		<option {if $lang_selector_array.de_DE}selected="selected"{/if} value='de_DE'>Deutsch (Deutschland)</option>
		<option {if $lang_selector_array.es_ES}selected="selected"{/if} value='es_ES'>Español (España)</option>
		</optgroup>
	</select>
	<noscript>
		<input type='submit' value='{t}Change Language{/t}' />
	</noscript>
</form>
