$(document).ready(function() {	
	var top_artists = $.jqplot('chart_top_artists', artists_data, {
		seriesDefaults: {
			renderer:$.jqplot.BarRenderer,
			pointLabels: { show: true, location: 'e', edgeTolerance: -10 },
	        shadow: false,
		color: '#ff0000',
	        rendererOptions: {
			barDirection: 'horizontal',
			barWidth: 15
	        }
	        },
	        axes: {
	            yaxis: {
	                renderer: $.jqplot.CategoryAxisRenderer,
			ticks: artists
	            },
		    xaxis: {
			renderer: $.jqplot.LinearAxisRenderer,
			tickInterval: artists_ti,
			min: 0,
			max: artists_max
		    }
	        }
	});
	
	var top_tracks = $.jqplot('chart_top_tracks', tracks_data, {
		seriesDefaults: {
			renderer:$.jqplot.BarRenderer,
			pointLabels: { show: true, location: 'e', edgeTolerance: -10 },
	        shadow: false,
		color: '#ff0000',
	        rendererOptions: {
			barDirection: 'horizontal',
			barWidth: 15
	        }
	        },
	        axes: {
	            yaxis: {
	                renderer: $.jqplot.CategoryAxisRenderer,
			ticks: tracks
	            },
		    xaxis: {
			renderer: $.jqplot.LinearAxisRenderer,
			tickInterval: tracks_ti,
			min: 0,
			max: tracks_max
		    }
	        }
	});
	
	var plays_by_days = $.jqplot('chart_plays_by_days', [date_data], {
		seriesDefaults: {
			color: '#ff0000',
			shadow: false,
			pointLabels: { show: true, location: 'w', edgeTolerance: -10 }
		},
		axesDefaults: {
			pad: 0
		},
		axes: {
		    xaxis: {
			renderer: $.jqplot.DateAxisRenderer
		    }
	        }
	});
	
	$('.jqplot-yaxis-tick').css('z-index', 255); 
});