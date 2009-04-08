{include file='header.tpl'}

<h2>{$name}</h2>

<div id="bio">
	{$bio_summary}
</div>

<ul id="albums" class="listcloud">
  {section name=i loop=$albums}
  <li>
    <dl>
      <dt><a href="album.php?artist={$name|urlencode}&album={$album[i]->name|urlencode}">
        {$album[i]->name}</a></dt>
	<dd>{$album[i]->releasedate}</dd>
    </dl>
  </li>
  {/section}
</ul>

{include file='footer.tpl'}
