{include file='header.tpl'}

<div about="{$id}" typeof="mo:MusicalArtist">

<h2 property="foaf:name" rel="foaf:page" rev="foaf:primaryTopic" resource="">{$name}</h2>

<div id="bio" property="bio:olb" datatype="">{$bio_summary}</div>

<ul id="albums" class="listcloud" rel="foaf:made" rev="foaf:maker">
  {section name=i loop=$albums}
  <li about="{$albums[i]->id}">
    <dl>
      <dt><a href="{$albums[i]->getURL()}">
        <img src="{$albums[i]->getAlbumArt()}" alt="{$albums[i]->name}" width="160" /></a></dt>
    <dd>{$albums[i]->getPlayCount()} plays</dd>
    </dl>
  </li>
  {/section}
</ul>
<div class="cleaner">&nbsp;</div>
{include file='footer.tpl'}
