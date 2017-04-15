/*
*    openrouteserver - Open source NDW route configurator en server
*    Copyright (C) 2014,2017 Jasper Vries
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

$(document).ready( function() {
	$('#addsegmentdialog').dialog( {
		autoOpen: false,
		title: 'Segmenten selecteren',
		height: $(window).height(),
		width: ($(window).width()/2),
		position: { my: "left top", at: "center top", of: window }
	});
	$('#addsegment').click( function () {
		$('#addsegmentdialog').dialog('open');
	});
	$('#segmentsearch').submit( function ( event ) {
		event.preventDefault();
		$.get('segmentsearch.php', { q: $('#segmentfind').val() } ).done(function( data ) {
			$('#segmentresult').html(data);
			$('.selectsegment').click( function () {
				var segment_id = $(this).parentsUntil('tr').parent().children('.selectsegment_id').html();
				var segment_length = $(this).parentsUntil('tr').parent().children('.selectsegment_length').html();
				
				var appendcontent = $('<tr><td class="removerowtd"></td><td><input type="hidden" name="segment_id[]" value="'+segment_id+'">'+segment_id+'</td><td><input type="hidden" name="segment_length[]" value="'+segment_length+'">'+segment_length+'</td><td><input type="text" name="segment_multiply[]" value="1"></td><td><input type="text" name="segment_add[]" value="0"></td></tr>');
				var appendcontent2 = $('<span class="removerow">[x]</span>');
				$(appendcontent).children('td.removerowtd').append(appendcontent2);
				appendcontent2.click( function () { $(this).parentsUntil('tr').parent().remove(); });
				$('#selectedsegments tbody').append(appendcontent);
			});
		});
	});
	$('.removerow').click( function () {
		$(this).parentsUntil('tr').parent().remove();
	});
});