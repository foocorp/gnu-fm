{include file='header.tpl'}

<h2>{$name}</h2>

<div id="bio">
	{$bio_summary}
</div>

<ul id="albums" class="listcloud">
  {section name=i loop=$albums}
  <li>
    <dl>
      <dt><a href="{$albums[i]->getURL()}">
        {$albums[i]->name}</a></dt>
	<dd>{$albums[i]->releasedate}{if ($image)}<img src="{$albums_art[i]}" alt="$albums[i]->name}" />{/if}</dd>
    <dd>{$albums[i]->getPlayCount()} plays</dd>
    </dl>
  </li>
  {/section}
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
