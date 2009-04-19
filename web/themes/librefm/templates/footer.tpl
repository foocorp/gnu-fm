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


	    <h4>Coming soon</h4>

	    <ul>
	    <li>Groups</li>
	    <li>Improved streaming support</li>
	    <li>Events</li>
	    <li>Improved artist pages</li>
	    <li>Album art</li>
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
<p>$Id$</p>
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
     </ul>

     <ul>
     <li>A <a href="http://foocorp.org/">FooCorp</a> thing.</li>
     <li><a href="http://autonomo.us/">autonomo.us</a></li>
     </ul>

     <p><a href='http://creativecommons.org/licenses/by-sa/3.0/' rel='license'><img src="{$base_url}/i/cc-by-sa.png" alt="Attribution-ShareAlike 3.0" /></a></p>

     <h6>Help translate libre.fm into your language</h6>

     <ul>
     <li><a href="http://aa.libre.fm">aa</a></li>
     <li><a href="http://ab.libre.fm">ab</a></li>
     <li><a href="http://ae.libre.fm">ae</a></li>
     <li><a href="http://af.libre.fm">af</a></li>
     <li><a href="http://ak.libre.fm">ak</a></li>
     <li><a href="http://am.libre.fm">am</a></li>
     <li><a href="http://an.libre.fm">an</a></li>
     <li><a href="http://ar.libre.fm">ar</a></li>
     <li><a href="http://as.libre.fm">as</a></li>
     <li><a href="http://av.libre.fm">av</a></li>
     <li><a href="http://ay.libre.fm">ay</a></li>
     <li><a href="http://az.libre.fm">az</a></li>
     <li><a href="http://ba.libre.fm">ba</a></li>
     <li><a href="http://be.libre.fm">be</a></li>
     <li><a href="http://bg.libre.fm">bg</a></li>
     <li><a href="http://bh.libre.fm">bh</a></li>
     <li><a href="http://bi.libre.fm">bi</a></li>
     <li><a href="http://bm.libre.fm">bm</a></li>
     <li><a href="http://bn.libre.fm">bn</a></li>
     <li><a href="http://bo.libre.fm">bo</a></li>
     <li><a href="http://br.libre.fm">br</a></li>
     <li><a href="http://bs.libre.fm">bs</a></li>
     <li><a href="http://ca.libre.fm">ca</a></li>
     <li><a href="http://ce.libre.fm">ce</a></li>
     <li><a href="http://ch.libre.fm">ch</a></li>
     <li><a href="http://co.libre.fm">co</a></li>
     <li><a href="http://cr.libre.fm">cr</a></li>
     <li><a href="http://cs.libre.fm">cs</a></li>
     <li><a href="http://cu.libre.fm">cu</a></li>
     <li><a href="http://cv.libre.fm">cv</a></li>
     <li><a href="http://cy.libre.fm">cy</a></li>
     <li><a href="http://cy.libre.fm">cy</a></li>
     <li><a href="http://da.libre.fm">da</a></li>
     <li><a href="http://de.libre.fm">de</a></li>
     <li><a href="http://dv.libre.fm">dv</a></li>
     <li><a href="http://dz.libre.fm">dz</a></li>
     <li><a href="http://ee.libre.fm">ee</a></li>
     <li><a href="http://el.libre.fm">el</a></li>
     <li><a href="http://en.libre.fm">en</a></li>
     <li><a href="http://eo.libre.fm">eo</a></li>
     <li><a href="http://es.libre.fm">es</a></li>
     <li><a href="http://et.libre.fm">et</a></li>
     <li><a href="http://eu.libre.fm">eu</a></li>
     <li><a href="http://fa.libre.fm">fa</a></li>
     <li><a href="http://ff.libre.fm">ff</a></li>
     <li><a href="http://fi.libre.fm">fi</a></li>
     <li><a href="http://fj.libre.fm">fj</a></li>
     <li><a href="http://fo.libre.fm">fo</a></li>
     <li><a href="http://fr.libre.fm">fr</a></li>
     <li><a href="http://fy.libre.fm">fy</a></li>
     <li><a href="http://ga.libre.fm">ga</a></li>
     <li><a href="http://gd.libre.fm">gd</a></li>
     <li><a href="http://gl.libre.fm">gl</a></li>
     <li><a href="http://gn.libre.fm">gn</a></li>
     <li><a href="http://gu.libre.fm">gu</a></li>
     <li><a href="http://gv.libre.fm">gv</a></li>
     <li><a href="http://ha.libre.fm">ha</a></li>
     <li><a href="http://he.libre.fm">he</a></li>
     <li><a href="http://hi.libre.fm">hi</a></li>
     <li><a href="http://ho.libre.fm">ho</a></li>
     <li><a href="http://hr.libre.fm">hr</a></li>
     <li><a href="http://ht.libre.fm">ht</a></li>
     <li><a href="http://hu.libre.fm">hu</a></li>
     <li><a href="http://hy.libre.fm">hy</a></li>
     <li><a href="http://hy.libre.fm">hy</a></li>
     <li><a href="http://hz.libre.fm">hz</a></li>
     <li><a href="http://ia.libre.fm">ia</a></li>
     <li><a href="http://id.libre.fm">id</a></li>
     <li><a href="http://ie.libre.fm">ie</a></li>
     <li><a href="http://ig.libre.fm">ig</a></li>
     <li><a href="http://ii.libre.fm">ii</a></li>
     <li><a href="http://ik.libre.fm">ik</a></li>
     <li><a href="http://io.libre.fm">io</a></li>
     <li><a href="http://is.libre.fm">is</a></li>
     <li><a href="http://it.libre.fm">it</a></li>
     <li><a href="http://iu.libre.fm">iu</a></li>
     <li><a href="http://ja.libre.fm">ja</a></li>
     <li><a href="http://jv.libre.fm">jv</a></li>
     <li><a href="http://ka.libre.fm">ka</a></li>
     <li><a href="http://kg.libre.fm">kg</a></li>
     <li><a href="http://ki.libre.fm">ki</a></li>
     <li><a href="http://kj.libre.fm">kj</a></li>
     <li><a href="http://kk.libre.fm">kk</a></li>
     <li><a href="http://kl.libre.fm">kl</a></li>
     <li><a href="http://km.libre.fm">km</a></li>
     <li><a href="http://kn.libre.fm">kn</a></li>
     <li><a href="http://ko.libre.fm">ko</a></li>
     <li><a href="http://kr.libre.fm">kr</a></li>
     <li><a href="http://ks.libre.fm">ks</a></li>
     <li><a href="http://ku.libre.fm">ku</a></li>
     <li><a href="http://kv.libre.fm">kv</a></li>
     <li><a href="http://kw.libre.fm">kw</a></li>
     <li><a href="http://ky.libre.fm">ky</a></li>
     <li><a href="http://la.libre.fm">la</a></li>
     <li><a href="http://lb.libre.fm">lb</a></li>
     <li><a href="http://lg.libre.fm">lg</a></li>
     <li><a href="http://li.libre.fm">li</a></li>
     <li><a href="http://ln.libre.fm">ln</a></li>
     <li><a href="http://lo.libre.fm">lo</a></li>
     <li><a href="http://lt.libre.fm">lt</a></li>
     <li><a href="http://lu.libre.fm">lu</a></li>
     <li><a href="http://lv.libre.fm">lv</a></li>
     <li><a href="http://mg.libre.fm">mg</a></li>
     <li><a href="http://mh.libre.fm">mh</a></li>
     <li><a href="http://mi.libre.fm">mi</a></li>
     <li><a href="http://mk.libre.fm">mk</a></li>
     <li><a href="http://ml.libre.fm">ml</a></li>
     <li><a href="http://mn.libre.fm">mn</a></li>
     <li><a href="http://mr.libre.fm">mr</a></li>
     <li><a href="http://ms.libre.fm">ms</a></li>
     <li><a href="http://mt.libre.fm">mt</a></li>
     <li><a href="http://my.libre.fm">my</a></li>
     <li><a href="http://na.libre.fm">na</a></li>
     <li><a href="http://nb.libre.fm">nb</a></li>
     <li><a href="http://nd.libre.fm">nd</a></li>
     <li><a href="http://ne.libre.fm">ne</a></li>
     <li><a href="http://ng.libre.fm">ng</a></li>
     <li><a href="http://nl.libre.fm">nl</a></li>
     <li><a href="http://nn.libre.fm">nn</a></li>
     <li><a href="http://no.libre.fm">no</a></li>
     <li><a href="http://nr.libre.fm">nr</a></li>
     <li><a href="http://nv.libre.fm">nv</a></li>
     <li><a href="http://ny.libre.fm">ny</a></li>
     <li><a href="http://oc.libre.fm">oc</a></li>
     <li><a href="http://oj.libre.fm">oj</a></li>
     <li><a href="http://om.libre.fm">om</a></li>
     <li><a href="http://or.libre.fm">or</a></li>
     <li><a href="http://os.libre.fm">os</a></li>
     <li><a href="http://pa.libre.fm">pa</a></li>
     <li><a href="http://pi.libre.fm">pi</a></li>
     <li><a href="http://pl.libre.fm">pl</a></li>
     <li><a href="http://ps.libre.fm">ps</a></li>
     <li><a href="http://pt.libre.fm">pt</a></li>
     <li><a href="http://qu.libre.fm">qu</a></li>
     <li><a href="http://rm.libre.fm">rm</a></li>
     <li><a href="http://rn.libre.fm">rn</a></li>
     <li><a href="http://ro.libre.fm">ro</a></li>
     <li><a href="http://ru.libre.fm">ru</a></li>
     <li><a href="http://rw.libre.fm">rw</a></li>
     <li><a href="http://sa.libre.fm">sa</a></li>
     <li><a href="http://sc.libre.fm">sc</a></li>
     <li><a href="http://sd.libre.fm">sd</a></li>
     <li><a href="http://se.libre.fm">se</a></li>
     <li><a href="http://sg.libre.fm">sg</a></li>
     <li><a href="http://si.libre.fm">si</a></li>
     <li><a href="http://sk.libre.fm">sk</a></li>
     <li><a href="http://sl.libre.fm">sl</a></li>
     <li><a href="http://sm.libre.fm">sm</a></li>
     <li><a href="http://sn.libre.fm">sn</a></li>
     <li><a href="http://so.libre.fm">so</a></li>
     <li><a href="http://sq.libre.fm">sq</a></li>
     <li><a href="http://sr.libre.fm">sr</a></li>
     <li><a href="http://ss.libre.fm">ss</a></li>
     <li><a href="http://st.libre.fm">st</a></li>
     <li><a href="http://su.libre.fm">su</a></li>
     <li><a href="http://sv.libre.fm">sv</a></li>
     <li><a href="http://sw.libre.fm">sw</a></li>
     <li><a href="http://ta.libre.fm">ta</a></li>
     <li><a href="http://te.libre.fm">te</a></li>
     <li><a href="http://tg.libre.fm">tg</a></li>
     <li><a href="http://th.libre.fm">th</a></li>
     <li><a href="http://ti.libre.fm">ti</a></li>
     <li><a href="http://tk.libre.fm">tk</a></li>
     <li><a href="http://tl.libre.fm">tl</a></li>
     <li><a href="http://tn.libre.fm">tn</a></li>
     <li><a href="http://to.libre.fm">to</a></li>
     <li><a href="http://tr.libre.fm">tr</a></li>
     <li><a href="http://ts.libre.fm">ts</a></li>
     <li><a href="http://tt.libre.fm">tt</a></li>
     <li><a href="http://tw.libre.fm">tw</a></li>
     <li><a href="http://ty.libre.fm">ty</a></li>
     <li><a href="http://ug.libre.fm">ug</a></li>
     <li><a href="http://uk.libre.fm">uk</a></li>
     <li><a href="http://ur.libre.fm">ur</a></li>
     <li><a href="http://uz.libre.fm">uz</a></li>
     <li><a href="http://ve.libre.fm">ve</a></li>
     <li><a href="http://vi.libre.fm">vi</a></li>
     <li><a href="http://vo.libre.fm">vo</a></li>
     <li><a href="http://wa.libre.fm">wa</a></li>
     <li><a href="http://wo.libre.fm">wo</a></li>
     <li><a href="http://xh.libre.fm">xh</a></li>
     <li><a href="http://yi.libre.fm">yi</a></li>
     <li><a href="http://yo.libre.fm">yo</a></li>
     <li><a href="http://za.libre.fm">za</a></li>
     <li><a href="http://zh.libre.fm">zh</a></li>
     <li><a href="http://zu.libre.fm">zu</a></li>
     </ul>

   </div>
</div>
</body>
</html>
