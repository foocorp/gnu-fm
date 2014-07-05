{include file='header.tpl' subheader='user-header.tpl'}

<center><h3><a href='{$base_url}/user-edit.php'>{t}Edit your profile{/t}</a> | <a href='{$base_url}/user-connections.php'>{t}Connections to other services{/t}</a> | {t}Delete plays{/t}</h3></center>

{if isset($errors)}
<div id="errors">
{section loop=$errors name=error}
	<p>{$errors[error]}</p>
{/section}
</div>
{/if}

<div id="user-delete-tracks">
  <p id="next-and-previous">
    {if $prevOffset >= 0}
    <a href="{$base_url}/user-delete-tracks.php?offset={$prevOffset}&amp;count={$scrobbleCount}">{t}Previous{/t} {$scrobbleCount}</a>
    {else}
    {t}Previous{/t} {$scrobbleCount}
    {/if}
    |
    {if $nextOffset >= 0}
    <a href="{$base_url}/user-delete-tracks.php?offset={$nextOffset}&amp;count={$scrobbleCount}">{t}Next{/t} {$scrobbleCount}</a>
    {else}
    {t}Next{/t} {$scrobbleCount}
    {/if}
  </p>
  <form action='{$base_url}/user-delete-tracks.php' method='post' onkeypress='return event.keyCode != 13;'>
    {section name=i loop=$scrobbles}
    <label><input type="checkbox" name="scrobble[]" value="{$scrobbles[i].time|escape:'html':'UTF-8'}	{$scrobbles[i].artist|escape:'html':'UTF-8'}	{$scrobbles[i].track|escape:'html':'UTF-8'}" />
      {$scrobbles[i].track|escape:'html':'UTF-8'} by {$scrobbles[i].artist|escape:'html':'UTF-8'}
      {if $scrobbles[i].albumurl} on the album, {$scrobbles[i].album|escape:'html':'UTF-8'}{/if}
      &mdash; {$scrobbles[i].timehuman}
    </label><br />
    {/section}
	<p>
	  <input type='submit' value='{t}Delete{/t}' />
	  <input name='submit' value='1' type='hidden' />
	  <input name='offset' value='{$scrobbleOffset}' type='hidden' />
	  <input name='count' value='{$scrobbleCount}' type='hidden' />
	</p>
</form>
</div>
<h3>{t}Help{/t}</h3>
<p>{t}Select the checkboxes of the plays that you want to delete then click the Delete button.{/t}</p>
<p>{t}Once you delete a play, it's gone permanently. We cannot recover deleted plays for you. So pleas be careful!{/t}</p>
{include file='footer.tpl'}
