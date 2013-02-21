{include file='header.tpl'}

<center>
{if isset($radio_session)}
{include file='flattr.tpl'}
{include file='player.tpl'}
<div id='error'></div>
<br /><br />
<div id='flattrstream' style='width: 50%; display: none;'>
	<div id='flattr'></div>
	<p>{t escape=no}Hey! If you really like this artist you can show your appreciation by donating to them via <a href='http://flattr.com'>flattr</a>.{/t}</p>
</div>
</center>
{else}
<h3>{t escape=no}Listen to music right here in your web browser!{/t}</h3>

<p>{t}To get started, simply enter the type of music you'd like to hear or select one of the common tags below:{/t}</p>

<p><a href="?tag=folk">Folk</a> <a href="?tag=rock">Rock</a> <a href="?tag=metal">Metal</a> <a href="?tag=classical">Classical</a> <a href="?tag=pop">Pop</a> <a href="?tag=blues">Blues</a> <a href="?tag=jazz">Jazz</a> <a href="?tag=punk">Punk</a> <a href="?tag=ambient">Ambient</a> <a href="?tag=electronic">Electronic</a></p>

<form method='get' action=''>
<div><label for="tag">{t}Custom tag:{/t}</label><input type="text" id="tag" name="tag" /></div><br />
<div><input type="checkbox" id="only_loved" name="only_loved" style="width: auto;" /> <label for="only_loved">{t}Only play me songs that other people love{/t}</label></div><br />
<div><input type="submit" value="{t}Listen{/t}" /></div></form>

<br />

<p>{t site=$site_name escape=no}Or listen to a random selection of the whole %1 community's favourite music on the <a href="?station=librefm://community/loved">%1 Community Station</a>{/t}</p>
{/if}

{include file='footer.tpl'}
