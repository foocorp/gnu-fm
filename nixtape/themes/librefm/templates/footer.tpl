    </div></div>
    <div class="yui-u" id="sidebar">
        <div style="padding: 10px;">

	{if !($logged_in)}

	<!-- put something here -->

	    <div id="adbard" style="margin: 1em auto 1em auto;">

	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '55fd9cde6d855a75f9ca43d854272f6b';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->

 
	    </div>

	{/if}

	   <h3>Top 40 libre artists</h3>
	   
	   {include file='tc-40.inc'}

	{if ($logged_in)}

	<!-- put something here -->

	    <div id="adbard" style="margin: 1em auto 1em auto;">

	    <!--Ad Bard advertisement snippet, begin -->

	    <script type='text/javascript'>
	     var ab_h = '4bcaab930d3bdfded68fd7be730d7db4';
     	     var ab_s = '55fd9cde6d855a75f9ca43d854272f6b';
     	    </script>
   	    
            <script type='text/javascript' src='http://cdn1.adbard.net/js/ab1.js'></script>

	    <!--Ad Bard, end -->

 
	    </div>

	    {/if}
     
        </div>
    </div>
</div></div>
	</div>
   <div id="ft" role="navigation">
	{include file='language-selector.tpl'}
	<br />

   <p><a href="http://identi.ca/tag/librefm">Blog</a> |
   <a href="http://libre.fm/">About Libre.fm</a> |
   <a href="http://bugs.libre.fm/">Bugs</a> |
   <a href="http://bugs.libre.fm/wiki/">Wiki</a> |
   <a href="http://turtle.libre.fm/stats.php">Stats</a> |
   <a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">List</a> | 
   <a href="/help/">Help</a> |
   <a href="mailto:support@libre.fm">Support</a></p>
   <p><strong>Check out our code:</strong> <a href="http://bzr.savannah.gnu.org/lh/librefm/">bzr repository at Savannah</a>.</p>

   <p><a href="http://playogg.org/"><img src="http://static.fsf.org/playogg/play_ogg_small.png" alt="Libre.fm is a proud supporter of the Play Ogg campaign" /></a></p>

   </div>
</div>
</body>
</html>
