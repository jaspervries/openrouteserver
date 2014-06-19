<?php
/*
*    openrouteserver - Open source NDW route configurator en server
*    Copyright (C) 2014 Jasper Vries
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
//get data
write_log('get data');
$datex = @file_get_contents($cfg_data['traveltime_url']);
if ($cfg_data['gz'] == TRUE) $datex = gzdecode($datex);
//process XML
if ($datex !== FALSE) {
	try {			
		$datex = @simplexml_load_string($datex);
		if ($datex !== FALSE) {
			$datex = $datex->children('soap', true)->Body->children(); //read soap envelope
			//get publication time
			$oldpublicationtime = $publicationtime;
			$publicationtime = $datex->d2LogicalModel->payloadPublication->publicationTime;
			$publicationtime = strtotime($publicationtime);
			//try again in 10 seconds
			if ($oldpublicationtime == $publicationtime) {
				write_log('duplicate pull');
				$time_mainloop_start = $time_mainloop_start - 48;
			}
			//get measurement data
			foreach ($datex->d2LogicalModel->payloadPublication->siteMeasurements as $siteMeasurement) {
				if ($siteMeasurement->measuredValue->measuredValue->basicData->travelTime->duration > 0) {
					$data[(string)$siteMeasurement->measurementSiteReference['id']]['val']  = (int)$siteMeasurement->measuredValue->measuredValue->basicData->travelTime->duration;
					//$data[(string)$siteMeasurement->measurementSiteReference['id']]['sd']   = (float)$siteMeasurement->measuredValue->measuredValue->basicData->travelTime['standardDeviation'];
					//$data[(string)$siteMeasurement->measurementSiteReference['id']]['scdq'] = (int)$siteMeasurement->measuredValue->measuredValue->basicData->travelTime['supplierCalculatedDataQuality'];
					if (!empty($siteMeasurement->measurementTimeDefault)) {
						$data[(string)$siteMeasurement->measurementSiteReference['id']]['time'] = strtotime($siteMeasurement->measurementTimeDefault);
					}
					else {
						$data[(string)$siteMeasurement->measurementSiteReference['id']]['time'] = $publicationtime;
					}
				}
			}
		}
	}
	catch (Exception $e) {
		write_log('XML exception: '.$e);
		//try again in 10 seconds
		$time_mainloop_start = $time_mainloop_start - 48;
	}
}
else {
	write_log('no pull data');
	//try again in 10 seconds
	$time_mainloop_start = $time_mainloop_start - 48;
}
?>