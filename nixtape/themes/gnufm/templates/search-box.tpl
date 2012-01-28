<div id='search'>
	<form action='/search.php' method='get'>
		<input name='search_term' type='text' value='{$search_term|escape:'html':'UTF-8'}'/>
		<select name='search_type'>
			<option value='artist' {if $search_type == 'artist'}selected{/if}>{t}Artist{/t}</option>
			<option value='user' {if $search_type == 'user'}selected{/if}>{t}User{/t}</option>
			<option value='tag' {if $search_type == 'tag'}selected{/if}>{t}Tag{/t}</option>
		</select>
		<input type='submit' value='{t}Search{/t}' id='search_button' />	
	</form>
</div>
