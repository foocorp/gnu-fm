{include file='header.tpl'}

<div id='user-edit'>
	<h2 property='dc:title'>Edit your profile</h2>

	<form action='{$base_url}/user-edit.php' method='post'>

	<div><label for='fullname'>{t}Full name:{/t}</label>

	<div class="formHelp">Enter your name here, if you want to.</div>

	<input name='fullname' id='fullname' value='{$fullname|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='location'>{t}Location:{/t}</label>
					<input name='location' id='location' value='{$location|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='homepage'>{t}Homepage URL:{/t}</label>

					<div class="formHelp">&nbsp;</div>
					<input name='homepage' id='homepage' value='{$homepage|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='avatar_uri'>{t}Avatar URL:{/t}
						<span><a href='#dfn_avatar_uri' rel='glossary'>{t}What's this?{/t}</a></span>
					</label>

					<div class="formHelp">&nbsp;</div>

					<input name='avatar_uri' id='avatar_uri' value='{$avatar_uri|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='bio'>{t}Mini Biography:{/t}</label>

					<div class="formHelp">&nbsp;</div>

					<textarea name='bio' id='bio'>{$bio|escape:'html':'UTF-8'}</textarea>
				</div>
				<div>
					<label for='id'>{t}WebID (FOAF){/t}
						<span><a href='#dfn_id' rel='glossary'>{t}What's this?{/t}</a></span>
					</label>

					<div class="formHelp">&nbsp;</div>

					<input name='id' id='id' value='{$id|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='laconica_profile'>{t}Laconica/identi.ca Profile:{/t}
						<span><a href='#dfn_laconica_profile' rel='glossary'>{t}What's this?{/t}</a></span>
					</label>


					<div class="formHelp">&nbsp;</div>

					<input onchange='laconicaChange();' onclick='laconicaClick();' name='laconica_profile' id='laconica_profile' value='{$laconica_profile|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='journal_rss'>{t}RSS Feed:{/t}
						<span><a href='#dfn_journal_rss' rel='glossary'>{t}What's this?{/t}</a></span>
					</label>


					<div class="formHelp">&nbsp;</div>

					<input name='journal_rss' id='journal_rss' value='{$journal_rss|escape:'html':'UTF-8'}' />
				</div>
				<div>
					<label for='password_1'>{t}Password:{/t}
						<span>{t}Leave this blank if you don't want to change your password.{/t}</span>
					</label>

					<div class="formHelp">&nbsp;</div>

					<input name='password_1' id='password_1' type='password' autocomplete="off" value='' />
				</div>
				<div>
					<label for='password_2'>{t}Confirm Password:{/t}</label>

					<div class="formHelp">&nbsp;</div>


					<input name='password_2' id='password_2' type='password' autocomplete="off" value='' />
				</div>

			</ul>

				<p>
					<input type='submit' value='Change' />
					<input name='submit' value='1' type='hidden' />
				</p>

	</form>

<!-- 

	<h3>{t}Help{/t}</h3>
	<dl>
		<dt id='dfn_location_uri'>{t}Location check{/t}</dt>
		<dd>{t escape=no}This feature looks up your location on <a href='http://www.geonames.org'>geonames</a>. You don't need to do it, but it will allow us find your latitude and longitude so we can add some great location-based features in the future.{/t}</dd>

		<dt id='dfn_avatar_uri'>{t}Avatar URL{/t}</dt>
		<dd>{t escape=no}The web address for a picture to represent you on libre.fm. It should not be more than 80x80 pixels. (64x64 is best.) If you leave this empty, libre.fm will use <a href='http://gravatar.com'>Gravatar</a> to find an image for you.{/t}</dd>

		<dt id='dfn_id'>WebID (FOAF)</dt>
		<dd>{t escape=no}A URI that represents you in RDF. See <a href='http://esw.w3.org/topic/WebID'>WebID</a> for details. If you don't know what this is, it's best to leave it blank.{/t}</dd>

		<dt id='dfn_laconica_profile'>Laconica/identi.ca Profile</dt>
		<dd>{t escape=no}The URL for your micro-blog on a <a href='http://laconi.ca/'>Laconica</a>-powered site such as <a href='http://identi.ca/'>identi.ca</a>.{/t}</dd>

		<dt id='dfn_journal_rss'>RSS Feed</dt>
		<dd>{t escape=no}An RSS feed which will be used to populate your journal on libre.fm. Defaults to your Laconica RSS feed if you provide your Laconica micro-blog address.{/t}</dd>

		<dt id='dfn_anticommercial'>Anticommercial</dt>
		<dd>{t escape=no}By enabling this option, you will not be shown advertisements or affiliate purchase links.{/t}</dd>
	</dl>
</div>

-->

{include file='footer.tpl'}
