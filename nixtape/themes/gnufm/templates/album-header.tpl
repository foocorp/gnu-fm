        <h2>
                <span rel="foaf:maker" rev="foaf:made" class="contributor">
                        <a about="{$artist->id}" typeof="mo:MusicArtist" property="foaf:name" class="url fn org"
                                rel="foaf:page" rev="foaf:primaryTopic" href="{$artist->getURL()}">{$artist->name}</a>
                        </span>
                        &#8212; 
                        <span class="album" property="dc:title" rel="foaf:page" rev="foaf:primaryTopic" resource="">{$name}</span>
			{if $edit_link}<a href="{$edit_link}"><small>[{t}Edit{/t}]</small></a>{/if}
        </h2>

