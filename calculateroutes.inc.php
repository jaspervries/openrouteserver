<?php
/*
*    openrouteserver - Open source NDW route configurator en server
*    Copyright (C) 2014 Jasper Vries; Gemeente Den Haag
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

//room for improvement
//improve calculation of missing segments by incorporating segment class

//loop through routes in database
write_log('calculate routes', $publicationtime);
$qry_routes = "SELECT `route_id`, `multiply`, `add` FROM `routes` WHERE `disabled` = 0";
$res_routes = mysqli_query($db['link'], $qry_routes);
if (mysqli_num_rows($res_routes)) {
	$datexfeed = array();
	while ($row_routes = mysqli_fetch_row($res_routes)) {
		//initialize variables
		$route_traveltime = 0;
		$segments_available = 0;
		$segments_total = 0;
		foreach ($cfg_class_colour as $class_id => $colour) {
			$allowable_traveltime_byclass[$class_id] = 0;
		}
		//get segments
		$qry_segments = "SELECT `route_mapping`.`segment_id`, `route_mapping`.`multiply`, `route_mapping`.`add`, `segments`.`length`, `segments`.`class` FROM `route_mapping` LEFT JOIN `segments` ON `route_mapping`.`segment_id` = `segments`.`segment_id` WHERE `route_mapping`.`route_id` = '".$row_routes[0]."'";
		$res_segments = mysqli_query($db['link'], $qry_segments);
		if (mysqli_num_rows($res_segments)) {
			while ($row_segments = mysqli_fetch_row($res_segments)) {
				//add segment length to total
				$segments_total += $row_segments[3] * $row_segments[1];
				//only add if valid value (i.e. larger than 0 and not older than 5 minutes)
				if ( ( $data[$row_segments[0]]['val'] > 0 ) && ( $data[$row_segments[0]]['time'] >= ( $publicationtime - (60 * 5) ) ) ) {
					//add and apply segment level adjustment
					$route_traveltime += ( $data[$row_segments[0]]['val'] * $row_segments[1] + $row_segments[2] * 60 );
					//add segment length
					$segments_available += $row_segments[3] * $row_segments[1];
					//calculate allowable traveltime per class
					foreach ($cfg_class[$row_segments[4]] as $class_id => $allowable_speed) {
						$allowable_traveltime_byclass[$class_id] += ( ($row_segments[3] * $row_segments[1]) / ($allowable_speed / 3.6) );
					}
//					write_log($row_segments[0].': '.$data[$row_segments[0]]['val'], $data[$row_segments[0]]['time']);
				}
			}
			//only store result if at least 75% of segments (by length) are available
			if ((($segments_total - $segments_available) / $segments_total) <= 0.25) {
				//compensate missing segments
				$route_traveltime = ( $route_traveltime * $segments_total / $segments_available );
				//apply route level adjustment
				$route_traveltime = round( $route_traveltime * $row_routes[1] + $row_routes[2] * 60 );
//				write_log($row_routes[0].' route time: '.$route_traveltime);
				/*
				 * calculate smoothed data
				*/
				$route_smoothed = 0;
				//get previous smoothed value
				$qry_smoothed = "SELECT `time`, `smoothed` FROM `route_history` WHERE `route_id` = '".$row_routes[0]."' ORDER BY `time` DESC LIMIT 1";
				$res_smoothed = mysqli_query($db['link'], $qry_smoothed);
				if (mysqli_num_rows($res_smoothed)) {
					//determine if within margin of 5 minutes
					$row_smoothed = mysqli_fetch_row($res_smoothed);
					if (($row_smoothed[0] >= ($publicationtime - (5*60))) && ($row_smoothed[1] > 0)) {
						$route_smoothed = round($route_traveltime * $reg['ema_alpha'] + $row_smoothed[1] * (1 - $reg['ema_alpha']));
					}
				}
				//otherwise store current value as smoothed value
				if ($route_smoothed == 0) {
					$route_smoothed = $route_traveltime;
				}
				//determine level of service
				$level_of_service = 0;
				for ($class_id = 1; $class_id < count($cfg_class_colour); $class_id++) {
					$allowable_traveltime_byclass[$class_id] = round( $allowable_traveltime_byclass[$class_id] * $segments_total / $segments_available );
//					write_log($class_id.' class time: '.$allowable_traveltime_byclass[$class_id]);
					if ($route_smoothed <= $allowable_traveltime_byclass[$class_id]) {
						$level_of_service = $class_id;
					}
					else break;
				}
//				write_log('los: '.$level_of_service);
				//store result
				$qry_update = "INSERT IGNORE INTO `route_history` SET `route_id` = '".$row_routes[0]."', `time` = '".$publicationtime."', `value` = '".$route_traveltime."', `smoothed` = '".$route_smoothed."', `level_of_service` = '".$level_of_service."'";
				mysqli_query($db['link'], $qry_update);
				//cache result for datex feed
				$datexfeed[] = array('id' => $cfg_site_prefix.$row_routes[0], 'duration' => $route_traveltime);
				$datexfeed[] = array('id' => $cfg_site_prefix.$row_routes[0].'_s', 'duration' => $route_smoothed);
			}
		}
	}
	//publish DATEX-II
	include_once('measureddatapublication.inc.php');
	$datex = createMeasuredDataPublication($datexfeed);
	$datex = gzencode($datex);
	$hdl = fopen('gui/datex/measureddatapublication.gz', 'w');
	fwrite($hdl, $datex);
	fclose($hdl);
}
?>