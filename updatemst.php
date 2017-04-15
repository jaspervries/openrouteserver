<?php
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

if (!function_exists('gzdecode')) { function gzdecode($data) {
   return gzinflate(substr($data,10,-8));
}}

//connect database
include('config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

//include log function
include('log.inc.php');
include('segmentclasses.cfg.php');

set_time_limit(0); 
ini_set("memory_limit","1G");

//get data
write_log('update MST');
echo "\r\nStart update MST";
$datex = @file_get_contents($cfg_data['mst_url']);
if ($cfg_data['gz'] == TRUE) $datex = gzdecode($datex);
//process XML
if ($datex !== FALSE) {
	try {			
		$datex = simplexml_load_string($datex);
		if ($datex !== FALSE) {
			$datex = $datex->children('SOAP', true)->Body->children(); //read soap envelope
			//get measurement data
			foreach ($datex->d2LogicalModel->payloadPublication->measurementSiteTable->measurementSiteRecord as $measurementSiteRecord) {
				
				if ($measurementSiteRecord->measurementSpecificCharacteristics->measurementSpecificCharacteristics->specificMeasurementValueType == 'travelTimeInformation') {
					$segmentlength = 0;
					$coordinates = '';
					$segmentid = $measurementSiteRecord['id'];
					$segmentname = $measurementSiteRecord->measurementSiteName->values->value;
					
					foreach ($measurementSiteRecord->measurementSiteLocation->locationContainedInItinerary as $locationContainedInItinerary) {
						foreach ($locationContainedInItinerary->location->supplementaryPositionalDescription->affectedCarriagewayAndLanes as $affectedCarriagewayAndLanes) {
							if ($affectedCarriagewayAndLanes->lengthAffected > 0) {
								$segmentlength += $affectedCarriagewayAndLanes->lengthAffected;
							}
						}
						$coordinates .= $locationContainedInItinerary->location->linearExtension->linearByCoordinatesExtension->linearCoordinatesStartPoint->pointCoordinates->latitude.','.$locationContainedInItinerary->location->linearExtension->linearByCoordinatesExtension->linearCoordinatesStartPoint->pointCoordinates->longitude."\n";
						$coordinates .= $locationContainedInItinerary->location->linearExtension->linearByCoordinatesExtension->linearCoordinatesEndPoint->pointCoordinates->latitude.','.$locationContainedInItinerary->location->linearExtension->linearByCoordinatesExtension->linearCoordinatesEndPoint->pointCoordinates->longitude."\n";
					}
					if ($segmentlength > 0) {
						//decide segment class
						$segmentclass = 'P';
						foreach ($cfg_class_G as $class) {
							if (stripos($segmentid, $class) === 0) {
								$segmentclass = 'G';
								break;
							}
						}
						if ($segmentclass == 'P') {
							foreach ($cfg_class_R as $class) {
								if (stripos($segmentid, $class) === 0) {
									$segmentclass = 'R';
									break;
								}
							}
						}
						
						//insert in database
						$qry = "INSERT INTO `segments` SET
						`segment_id` = '".mysqli_real_escape_string($db['link'], $segmentid)."',
						`name` = '".mysqli_real_escape_string($db['link'], $segmentname)."',
						`length` = '".mysqli_real_escape_string($db['link'], $segmentlength)."',
						`class` = '".$segmentclass."',
						`coordinates` = '".mysqli_real_escape_string($db['link'], $coordinates)."'
						ON DUPLICATE KEY UPDATE
						`name` = '".mysqli_real_escape_string($db['link'], $segmentname)."',
						`length` = '".mysqli_real_escape_string($db['link'], $segmentlength)."',
						`class` = '".$segmentclass."',
						`coordinates` = '".mysqli_real_escape_string($db['link'], $coordinates)."'";
						mysqli_query($db['link'], $qry);
						echo mysqli_error($db['link']);
					}
				}
			}
		}
	}
	catch (Exception $e) {
		echo "\r\nXML exception: ".$e;
		write_log('XML exception: '.$e);
	}
}
else {
	write_log('no data');
	echo "\r\nNo data";
}
echo "\r\nComplete";
?>