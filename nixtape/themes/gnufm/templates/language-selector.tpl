<form method='get' action=''>
	<label for='lang'>{t}Preferred language:{/t} </label>
	<select name='lang' id='lang' onchange='this.form.submit()'>
		<optgroup label="{t}Please select your language{/t}">
		<option {if $lang_selector_array.en_US}selected="selected"{/if} value='en_US'>English</option>
		<option {if $lang_selector_array.ca_ES}selected="selected"{/if} value='ca_ES'>Català</option>
		<option {if $lang_selector_array.cs_CZ}selected="selected"{/if} value='cs_CZ'>Čeština</option>
		<option {if $lang_selector_array.cy_GB}selected="selected"{/if} value='cy_GB'>Cymraeg</option>
		<option {if $lang_selector_array.de_DE}selected="selected"{/if} value='de_DE'>Deutsch</option>
		<option {if $lang_selector_array.es_ES}selected="selected"{/if} value='es_ES'>Español</option>
		<option {if $lang_selector_array.eo}selected="selected"{/if} value='eo'>Esperanto</option>
		<option {if $lang_selector_array.fi_FI}selected="selected"{/if} value='fi_FI'>Finnish</option>
		<option {if $lang_selector_array.fr_FR}selected="selected"{/if} value='fr_FR'>Français</option>
		<option {if $lang_selector_array.gl_ES}selected="selected"{/if} value='gl_ES'>Galician</option>
		<option {if $lang_selector_array.nl_NL}selected="selected"{/if} value='nl_NL'>Nederlands</option>
		<option {if $lang_selector_array.pt_BR}selected="selected"{/if} value='pt_BR'>Português Brasileiro</option>
		<option {if $lang_selector_array.ru_RU}selected="selected"{/if} value='ru_RU'>Pусский язык</option>
		<option {if $lang_selector_array.sq_AL}selected="selected"{/if} value='sq_AL'>Shqip</option>
		<option {if $lang_selector_array.sl_SI}selected="selected"{/if} value='sl_SI'>Slovenščina</option>
		</optgroup>
	</select>
	<noscript>
		<input type='submit' value='{t}Change Language{/t}' />
	</noscript>
</form>
