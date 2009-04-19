{include file='header.tpl'}

<div about="{$id}" typeof="mo:MusicalArtist">

<h2 property="foaf:name" rel="foaf:page" rev="foaf:primaryTopic" resource="">{$name}</h2>

{if $bio_summary}
<div id="bio" property="bio:olb" datatype="">{$bio_summary}</div>
{/if}

<ul id="albums" class="listcloud" rel="foaf:made" rev="foaf:maker">
  {section name=i loop=$albums}
  <li about="{$albums[i]->id}" property="dc:title" content="{$albums[i]->name}">
    <dl>
      <dt><a rel="foaf:page" href="{$albums[i]->getURL()}">
        <span{if $albums[i]->getAlbumArt() != '/i/qm50.png'} about="{$albums[i]->id}" rel="foaf:depiction"{/if}><img src="{$albums[i]->getAlbumArt()}" alt="{$albums[i]->name}" width="160" /></span></a></dt>
    <dd>{$albums[i]->getPlayCount()} plays</dd>
    </dl>
  </li>
  {/section}
</ul>

</div>

<div class="cleaner">&nbsp;</div>

{include file='footer.tpl'}

