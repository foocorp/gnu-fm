<div id='search'>
     
     <h3>Search</h3>
     
	<form action='{$base_url}/search.php' method='get'>
		<p><input name='search_term' type='text' size="10" value='{if isset($search_term)}{$search_term|escape:'html':'UTF-8'}{/if}'/>
		<select name='search_type'>
			<option value='artist' {if isset($search_type) && $search_type == 'artist'}selected{/if}>{t}Artist{/t}</option>
			<option value='user' {if isset($search_type) && $search_type == 'user'}selected{/if}>{t}User{/t}</option>
			<option value='tag' {if isset($search_type) && $search_type == 'tag'}selected{/if}>{t}Tag{/t}</option>
		</select>
		<input type='submit' value='{t}Search{/t}' id='search_button' /></p>	
	</form>
</div>
