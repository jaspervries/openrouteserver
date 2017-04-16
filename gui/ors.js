/*
*    openrouteserver - Open source NDW route configurator en server
*    Copyright (C) 2014,2017 Jasper Vries; 2014 Gemeente Den Haag
*
*    This program is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License, or
*    (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License along
*    with this program; if not, write to the Free Software Foundation, Inc.,
*    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

google.load("visualization", "1", {packages:["corechart"]});

function drawChart(id, data) {
	console.log(document.getElementById(id));
	var chart = new google.visualization.LineChart(document.getElementById(id));
	
	var options = {
		width: 700,
        height: 400,
		chartArea: {height:'75%', width:'80%'},
        legend: {'position': 'bottom'},
		series: {
			2: {
				targetAxisIndex: 1,
				lineWidth: 1
			},
			3: {
				targetAxisIndex: 0,
				lineWidth: 1
			}
		},
		vAxes: [
			{title: 'Reistijd [min]',
			minValue: -1,
			gridlines: {count: 7}
			},
			{title: 'Level of Service',
			direction: -1,
			minValue: 0,
			maxValue: 3,
			gridlines: {count: 4}}	
		],
		hAxis: {
			title: 'Tijd'	
		}
	};
	chart.draw(data, options);
}

function showChart(id) {
	//add dialog div
	var div_id = 'dialog' + id;
	var chart_id = 'chart' + id;
	//add html div
	if($("#" + div_id).length == 0) {
		$('body').append('<div class="dialog" id="' + div_id + '">Grafiek wordt geladen</div>');
	}
	//create dialog
	$('#' + div_id).dialog({
		height: 460,
		width: 740,
		title: 'Laden...',
		close: function() {
			//remove dialog on close
			$(this).remove();
		}
	});
	
	//load dialog content
	$.getJSON('chart.php', { q: id } ).done(function( data ) {
		$('#' + div_id).dialog('option', 'title', data['name']);
		$('#' + div_id).html('<div id="' + chart_id + '" style="width: 700px; height: 400px;"></div>');
		
		var dataTable = new google.visualization.DataTable();
		dataTable.addColumn('datetime', 'tijdstip');
		dataTable.addColumn('number', 'reistijd');
		dataTable.addColumn('number', 'gefilterde reistijd');
		dataTable.addColumn('number', 'level of service');
		dataTable.addColumn('number', 'freeflow');
		
		$.each(data.values, function(index, value) {
			dataTable.addRow([new Date(value[0], value[1], value[2], value[3], value[4], value[5]), 
			{v: value[6], f: String(value[7])}, 
			{v: value[8], f: String(value[9])}, 
			parseInt(value[10]),
			{v: value[11], f: String(value[12])}, 
			]);
		});
		
		drawChart(chart_id, dataTable);
	});
	
}

function loadRoutes() {
	$.get('routes.html').done(function(data) {
		$('#routes').html(data);
	});
}

$(document).ready( function() {
	$('body').html('<div id="routes"></div>');
	
	//sluit alle dialogs wanneer op delete (46) wordt gedrukt
	$(document).keyup(function( event ) {
		if (event.which == 46) {
			//behalve wanneer in input veld
			if (!($('input').is(':focus'))) {
				$('.dialog').dialog('close');
			}
		}
	});
	
	loadRoutes();
	//update every x secs
	setInterval( function () {	
		loadRoutes();
	} , 20000); //refresh interval [ms]
});