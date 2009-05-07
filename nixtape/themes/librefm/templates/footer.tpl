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

	    <div id="adbard">

	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '55fd9cde6d855a75f9ca43d854272f6b';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->
	    
	    </div>

	{if $random_group}
	    <h4>Featured group</h4>
	    <p style="margin:1em auto" about="{$random_group->id}" typeof="foaf:Group">
		<a rel="foaf:homepage" href="{$random_group->getURL()|escape:'html':'UTF-8'}">
			<img src="{$random_group->getAvatar()}" rev="foaf:depiction" resource="{$random_group->id}"
			alt="" height="32" width="32" style="margin: 0 0.67em 0.33em 0;float:left;border:0" />
			<b about="{$random_group->id}" property="foaf:name">{if $random_group->fullname}{$random_group->fullname|escape:'html':'UTF-8'}{else}{$random_group->name|escape:'html':'UTF-8'}{/if}</b></a>
		<br />
		<small property="olb:bio">{$random_group->bio|escape:'html':'UTF-8'}</small>
		<br style="clear:left" />
	    </p>
	{/if}
	
	    <h4>Coming soon</h4>

	    <ul>
	    <li>Improved streaming support</li>
	    <li>Events</li>
	    <li>Improved artist pages</li>
	    </ul>

	    <h3>Developers</h3>

	    <p>If you'd like to get started hacking on Libre.fm, <a href="https://savannah.nongnu.org/my/groups.php?words=libre.fm#searchgroup">join the project today</a>.</p>

	    <h3>Calling all Free Culture artists</h3>

	    <p>Get involved on the <a
	    href="http://lists.autonomo.us/mailman/listinfo/libre-fm">mailing
	    list</a> and <a
	    href="https://savannah.nongnu.org/bugs/?group=librefm">tell
	    us the features</a> <strong>you want to see</strong>.</p>

        </div>
    </div>
{/if}
</div></div>
<div class="yui-g" id="artists">

  <strong><a href="http://libre.fm/contact/">Talk to us</a></strong> if you're in a band, represent a
  label or music service, we'd like to talk ideas and
  possibilities. While our intention is eventually provide download
  and streaming services for freely-licensed music, we are also
  interested in linking all bands to respectable DRM-free music
  services.
  
</div>
<div class="yui-g">
    <div class="yui-u first" id="links">
    <p>Get started with Libre.fm -- <a href="http://ideas.libre.fm/index.php/Using_turtle">We have help</a></p>
    </div>
    <div class="yui-u" id="moarlinks">
<p></p>
    </div>
</div>

	</div>
   <div id="ft" role="navigation">

     <ul>
       <li class="copy">&copy; 2009 <a href="http://libre.fm/">Libre.fm</a> Project</li>
       <li><a href="http://libre.fm/contributors/">Contributors</a></li>
       <li><a href="http://libre.fm/licensing/">Licensing information</a></li>
       <li><a href="http://libre.fm/developer/">Developers</a></li>
       <li><a href="http://libre.fm/api/">API</a></li>
       <li><a href="http://libre.fm/download/">Download</a></li>
       <li><a href="http://libre.fm/translations/">Help translate Libre.fm</a></li>
     </ul>

     <ul>
     <li>A <a href="http://foocorp.org/">FooCorp</a> thing.</li>
     <li><a href="http://autonomo.us/">autonomo.us</a></li>
     <li><a href="http://svn.savannah.gnu.org/viewvc/branches/stable/nixtape/?root=librefm">Powered by Nixtape</a></li>
     </ul>

     <p><a href='http://creativecommons.org/licenses/by-sa/3.0/' rel='license'><img src="{$base_url}/i/cc-by-sa.png" alt="Attribution-ShareAlike 3.0" /></a></p>

   </div>
</div>
</body>
</html>
