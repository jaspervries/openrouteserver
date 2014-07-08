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
if (!function_exists(createMeasuredDataPublication)) { function createMeasuredDataPublication($datexfeed) {
	global $cfg_supplier_prefix;
	//set constant publicationTime for all values
	$publicationTime = date('c');
	//generic header
	$xml = new SimpleXMLElement('<SOAP-ENV_Envelope xmlns_SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"></SOAP-ENV_Envelope>');
		$xml->addChild('SOAP-ENV_Header');
	$soap = $xml->addChild('SOAP-ENV_Body');
	$d2LogicalModel = $soap->addChild('d2LogicalModel');
		$d2LogicalModel->addAttribute('xmlns_xsd', 'http://www.w3.org/2001/XMLSchema');
		$d2LogicalModel->addAttribute('xmlns_xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$d2LogicalModel->addAttribute('xmlns', 'http://datex2.eu/schema/2/2_0');
		$d2LogicalModel->addAttribute('modelBaseVersion', '2');
		$d2LogicalModel->addAttribute('xsi_schemaLocation', 'http://datex2.eu/schema/2/2_0 http://www.ndw.nu/DATEXII/DATEXIISchema_2_2_1.xsd');
	$exchange = $d2LogicalModel->addChild('exchange');
		$exchange->addAttribute('xmlns', 'http://datex2.eu/schema/2/2_0');
	$supplierIdentification = $exchange->addChild('supplierIdentification');
		$supplierIdentification->addChild('country', 'nl');
		$supplierIdentification->addChild('nationalIdentifier', $cfg_supplier_prefix);
	$payloadPublication = $d2LogicalModel->addChild('payloadPuplication');
		$payloadPublication->addAttribute('xmlns', 'http://datex2.eu/schema/2/2_0');
		$payloadPublication->addAttribute('lang', 'nl');
		$payloadPublication->addAttribute('xsi_type', 'MeasuredDataPublication');
		$payloadPublication->addChild('publicationTime', $publicationTime);
	$publicationCreator = $payloadPublication->addChild('publicationCreator');
		$publicationCreator->addChild('country', 'nl');
		$publicationCreator->addChild('nationalIdentifier', $cfg_supplier_prefix);
	$measurementSiteTableReference = $payloadPublication->addChild('measurementSiteTableReference');
		$measurementSiteTableReference->addAttribute('id', $cfg_supplier_prefix.'_MST');
		$measurementSiteTableReference->addAttribute('version', '1');
		$measurementSiteTableReference->addAttribute('targetClass', 'MeasurementSiteTable');
	$headerInformation = $payloadPublication->addChild('headerInformation');
		$headerInformation->addChild('confidentiality', 'noRestriction');
		$headerInformation->addChild('informationStatus', 'real');
	//for each route
	foreach($datexfeed as $row) {
		$siteMeasurements = $payloadPublication->addChild('siteMeasurements');
		$measurementSiteReference = $siteMeasurements->addChild('measurementSiteReference');
			$measurementSiteReference->addAttribute('id', $row['id']); //id
			$measurementSiteReference->addAttribute('version', '1');
			$measurementSiteReference->addAttribute('targetClass', 'MeasurementSiteRecord');
		$measurementTimeDefault = $siteMeasurements->addChild('measurementTimeDefault', $publicationTime); //time
		$measuredValue = $siteMeasurements->addChild('measuredValue');
			$measuredValue->addAttribute('index', '1');
			$measuredValue->addAttribute('xsi_type', '_SiteMeasurementsIndexMeasuredValue');
		$measuredValue = $measuredValue->addChild('measuredValue');
			$measuredValue->addAttribute('xsi_type', 'MeasuredValue');
		$basicData = $measuredValue->addChild('basicData');
			$basicData->addAttribute('xsi_type', 'TravelTimeData');
			$basicData->addChild('travelTimeType', 'best');
		$travelTime = $basicData->addChild('travelTime');
			$travelTime->addAttribute('xsi_type', 'DurationValue');
			if (!empty($row['quality'])) $travelTime->addAttribute('supplierCalculatedDataQuality', $row['quality']); //quality
			$travelTime->addChild('duration', $row['duration']); //duration
	}
	//format XML
	$dom = dom_import_simplexml($xml)->ownerDocument;
	$dom->formatOutput = true;
	$xml = $dom->saveXML();
	//replace _ for :
	$xml = str_replace(array('xmlns_', 'xsi_', 'SOAP-ENV_'), array('xmlns:', 'xsi:', 'SOAP-ENV:'), $xml);
	return($xml);
}}
?>