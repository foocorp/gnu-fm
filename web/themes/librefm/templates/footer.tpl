    </div></div>
{if !$profile}
    <div class="yui-u" id="sidebar">
        <div style="padding: 10px;">
            <h3>Explore popular artists</h3>
            <ul class="tagcloud">
    {section name=i loop=$tagcloud}
                <li style='font-size:{$tagcloud[i].size}'><a href='/artist/{$tagcloud[i].artist|urlencode}' title='This artist was played {$tagcloud[i].count} times' rel='tag'>{$tagcloud[i].artist}</a></li>
    {/section}
            </ul>     
        </div>
    </div>
{/if}
</div></div>
<div class="yui-g" id="artists">

  <strong><a href="/contact/">Talk to us</a></strong> if you're in a band, represent a
  label or music service, we'd like to talk ideas and
  possibilities. While our intention is eventually provide download
  and streaming services for freely-licensed music, we are also
  interested in linking all bands to respectable DRM-free music
  services.
  
</div>
<div class="yui-g">
    <div class="yui-u first" id="links">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas sit amet metus. Nunc quam elit, posuere nec, auctor in, rhoncus quis, dui. Aliquam erat volutpat. Ut dignissim, massa sit amet dignissim cursus, quam lacus feugiat.    </div>
    <div class="yui-u" id="moarlinks">
Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Maecenas sit amet metus. Nunc quam elit, posuere nec, auctor in, rhoncus quis, dui. Aliquam erat volutpat. Ut dignissim, massa sit amet dignissim cursus, quam lacus feugiat.    </div>
</div>

	</div>
   <div id="ft" role="navigation">

     <ul>
       <li class="copy">&copy; 2009 Libre.fm Project</li>
       <li><a href="/contributors/">Contributors</a></li>
       <li><a href="/licensing/">Licensing information</a></li>
       <li><a href="/developer/">Developers</a></li>
       <li><a href="/api/">API</a></li>
       <li><a href="/download/">Download</a></li>
     </ul>

     <p><a href='http://creativecommons.org/licenses/by-sa/3.0/' rel='license'><img src="{$base_url}/i/cc-by-sa.png" alt="Attribution-ShareAlike 3.0" /></a></p>

   </div>
</div>
</body>
</html>
