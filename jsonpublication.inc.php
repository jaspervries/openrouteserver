<?php
/*
*    openrouteserver - Open source NDW route configurator en server
*    Copyright (C) 2014 Gemeente Den Haag
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
if (!function_exists('createJsonPublication')) { function createJsonPublication($datexfeed) {
	//set constant publicationTime for all values
	$publicationTime = date('c');
	
	//generic
	$json = array('time' => $publicationTime,
	'data' => array());
	//for each route
	foreach($datexfeed as $row) {
		if (isset($row['quality']) && ($row['quality'] < 100)) {
			$json['data'][] = array( 'id' => $row['id'], //id
								'duration' => (int) $row['duration'], //duration
								'staticReferenceValue' => (int) $row['freeflow'], //freeflow
								'quality' => (int) $row['quality']); //quality
		}
		else {
			$json['data'][] = array( 'id' => $row['id'], //id
								'duration' => (int) $row['duration'], //duration
								'staticReferenceValue' => (int) $row['freeflow']); //freeflow
		}
	}
	$json = json_encode($json);
	return($json);
}}
?>