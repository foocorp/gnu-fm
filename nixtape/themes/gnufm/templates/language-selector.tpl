<form method='get' action=''>
	<label for='lang'>{t}Preferred language:{/t} </label>
	<select name='lang' id='lang' onchange='this.form.submit()'>
		<optgroup label="{t}Please select your language{/t}">
		<option {if array_key_exists('en_US', $lang_selector_array)}selected="selected"{/if} value='en_US'>English</option>
		<option {if array_key_exists('ca_ES', $lang_selector_array)}selected="selected"{/if} value='ca_ES'>Català</option>
		<option {if array_key_exists('cs_CZ', $lang_selector_array)}selected="selected"{/if} value='cs_CZ'>Čeština</option>
		<option {if array_key_exists('cy_GB', $lang_selector_array)}selected="selected"{/if} value='cy_GB'>Cymraeg</option>
		<option {if array_key_exists('de_DE', $lang_selector_array)}selected="selected"{/if} value='de_DE'>Deutsch</option>
		<option {if array_key_exists('es_ES', $lang_selector_array)}selected="selected"{/if} value='es_ES'>Español</option>
		<option {if array_key_exists('eo', $lang_selector_array)}selected="selected"{/if} value='eo'>Esperanto</option>
		<option {if array_key_exists('fi_FI', $lang_selector_array)}selected="selected"{/if} value='fi_FI'>Finnish</option>
		<option {if array_key_exists('fr_FR', $lang_selector_array)}selected="selected"{/if} value='fr_FR'>Français</option>
		<option {if array_key_exists('gl_ES', $lang_selector_array)}selected="selected"{/if} value='gl_ES'>Galician</option>
		<option {if array_key_exists('nl_NL', $lang_selector_array)}selected="selected"{/if} value='nl_NL'>Nederlands</option>
		<option {if array_key_exists('pt_BR', $lang_selector_array)}selected="selected"{/if} value='pt_BR'>Português Brasileiro</option>
		<option {if array_key_exists('ru_RU', $lang_selector_array)}selected="selected"{/if} value='ru_RU'>Pусский язык</option>
		<option {if array_key_exists('sq_AL', $lang_selector_array)}selected="selected"{/if} value='sq_AL'>Shqip</option>
		<option {if array_key_exists('sl_SI', $lang_selector_array)}selected="selected"{/if} value='sl_SI'>Slovenščina</option>
		</optgroup>
	</select>
	<noscript>
		<input type='submit' value='{t}Change Language{/t}' />
	</noscript>
</form>
