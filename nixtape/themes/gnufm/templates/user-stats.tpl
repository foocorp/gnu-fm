{include file='header.tpl' subheader='user-header.tpl'}
<script type="text/javascript" src="{$base_url}/js/stats/user.js"></script>
<script type="text/javascript" src="{$base_url}/js/jquery.jqplot.min.js"></script> 
<script type="text/javascript" src="{$base_url}/js/plugins/jqplot.barRenderer.min.js"></script>
<script type="text/javascript" src="{$base_url}/js/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="{$base_url}/js/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script type="text/javascript" src="{$base_url}/js/plugins/jqplot.pointLabels.min.js"></script>

<ul>
	<li><a href="#stats_by_artist">{t}Most played artists{/t}</a></li>
	<li><a href="#stats_by_track">{t}Top tracks{/t}</a></li>
	<li><a href="#stats_by_day">{t}Scrobbles by day{/t}</a></li>
</ul>


<h3>Total tracks: {$totaltracks}</h3>

<h4 id="stats_by_artist">{t name=$me->name|escape:'html':'UTF-8'}%1's most played artists{/t} {$timeperiod}</h4>
<div id="chart_top_artists" style="height:{$topartistspx}px;width:95%;"
     class="chart_space horizontal_chart_axes" about="{$me->id}"></div>

<h4 id="stats_by_track">{t name=$me->name|escape:'html':'UTF-8'}%1's top tracks{/t} {$timeperiod}</h4>
<div id="chart_top_tracks" style="height:{$toptrackspx}px;width:95%;"
     class="chart_space horizontal_chart_axes" about="{$me->id}"></div>

<h4 id="stats_by_day">{t name=$me->name|escape:'html':'UTF-8'}%1's scrobbles by day{/t}</h4>
<div id="chart_plays_by_days" style="height:400px;width:95%;"
     class="chart_space about="{$me->id}"></div>

<script type="text/javascript">
	var artists = {$graphtopartists->artists};
	var artists_data = {$graphtopartists->artists_data};
	var artists_ti = {$graphtopartists->tick_interval};
	var artists_max = {$graphtopartists->max_x_axis};
	var tracks = {$graphtoptracks->tracks};
	var tracks_data = {$graphtoptracks->tracks_data};
	var tracks_ti = {$graphtoptracks->tick_interval};
	var tracks_max = {$graphtoptracks->max_x_axis};
	var date_data = {$graphplaysbydays->plays_by_days};
	/* Chart Formatting */
	var def_bar_width = 18;
</script> 
{include file='footer.tpl'}
