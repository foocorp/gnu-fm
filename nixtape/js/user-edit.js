/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Free Software Foundation, Inc

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

function unrecognised ( msg )
{
	$('#location_uri')[0].value = '';
	$('#location_uri_label').removeClass('ok');
	$('#location_uri_label').addClass('no');
	$('#location_uri_label').text( msg );
	return 0;
}

function UpdateLocationLabel ()
{
	if ($('#location_uri')[0].value)
	{
		$('#location_uri_label').text($('#location_uri')[0].value);
	}
}

function LocationCheck ()
{
	if ( !$('#location').val() )
	{
		return unrecognised("You must enter a location.");
	}

	ajaxLoading(1);
	
	var ajaxSuccess = function (data, status)
		{
			ajaxLoading(0);

			if (! data.geonames[0])
			{
				return unrecognised("This location was unrecognisable.");
			}

			var list;

			if ($('#chooser_list')[0])
			{
				$('#chooser_list').empty();
				$('#chooser_list').show();
				list = $('#chooser_list')[0];
			}
			else
			{
				list = document.createElement('UL');
				list.id = 'chooser_list';
				$('#chooser')[0].appendChild(list);
			}
			
			for (var g in data.geonames)
			{
				var G = data.geonames[g];
				
				var fullName = G.name;
				if (G.adminName3) fullName += ", " + G.adminName3;
				if (G.adminName2) fullName += ", " + G.adminName2;
				if (G.adminName1) fullName += ", " + G.adminName1;
				if (G.countryName) fullName += ", " + G.countryName;
				
				var coords = G.lat.toFixed(2) + ';' + G.lng.toFixed(2);
				
				var shortName = G.name;
				if (G.countryCode == 'US')
					shortName += ", " + G.adminCode1 + ", USA";
				else
					shortName += ", " + G.countryCode;

				var item = document.createElement('LI');
				var label1 = document.createElement('B');
				label1.appendChild(document.createTextNode(fullName));
				item.appendChild(label1);
				item.appendChild(document.createTextNode(' '));
				var label2 = document.createElement('SMALL');
				label2.appendChild(document.createTextNode('['+coords+']'));
				item.appendChild(label2);
				
				item.setAttribute('data-geoname', 'http://sws.geonames.org/' + G.geonameId + '/');
				item.setAttribute('data-geoname-name', shortName);
				item.setAttribute('data-geoname-coords', coords);
				
				item.onclick = function (e) {
					if (!e) var e = window.event;
					var tg = (window.event) ? e.srcElement : e.target;
					var geoname = $(tg).closest('li').attr('data-geoname');
					$('#location_uri')[0].value = geoname;
					$('#chooser_list').empty();
					$('#chooser_list').hide();
					UpdateLocationLabel();
					$('#location_uri_label').addClass('ok');
					$('#location_uri_label').removeClass('no');
				}		
				$(item).hover(function ()
					{
						$(this).addClass('hover');
					}, 
					function ()
					{
						$(this).removeClass('hover');
					}
					);
				list.appendChild(item);
			}
		};
		
	var ajaxError = function (XMLHttpRequest, textStatus, errorThrown)
		{
			ajaxLoading(0);
			return unrecognised("Request error: " + textStatus);
		};

	$.ajax({
			'type' : 'GET' ,
			'url' : "/utils/location-ws.php" ,
			'data' : { 'q' : $('#location').val() },
			'dataType' : 'json' ,
			'timeout' : 30000 ,
			'success' : ajaxSuccess ,
			'error' : ajaxError 
		});
}

function ajaxLoading (l)
{
	if (l==1)
	{
		if ($('#loading')[0])
		{
			$('#loading').show();
		}
		else
		{
			var loading = document.createElement('IMG');
			loading.id = 'loading';
			document.body.appendChild(loading);
			loading.src = '/i/loading.gif';
			loading.style.height = '32px';
			loading.style.width = '32px';
			loading.style.position = 'absolute';
			loading.style.top = '50%';
			loading.style.left = '50%';
			loading.style.marginTop = '-16px';
			loading.style.marginLeft = '-16px';
		}
	}
	else
	{
		$('#loading').hide();
	}
}

function webidLookup ()
{
	window.open("/utils/webid-finder-ws/form.php?javascript=id",
		"webid-finder",
		"status=1,resizable=1,scrollbars=1,width=600,height=500");
}

function laconicaClick ()
{
	if ($('#laconica_profile')[0].value == 'http://identi.ca/example')
		$('#laconica_profile')[0].value = '';
}

function laconicaChange ()
{
	if ($('#laconica_profile')[0].value.match(/^http:\/\/.+\/.+/))
	{
		$('#journal_rss')[0].value = $('#laconica_profile')[0].value + '/rss';
	}
}

UpdateLocationLabel();

if ($('#laconica_profile')[0].value == '')
	$('#laconica_profile')[0].value = 'http://identi.ca/example';
