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

//connect database
include('../config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

$data = array();
$qry = "SELECT `route_id`, `name` FROM `routes` WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['q'])."'";
$res = mysqli_query($db['link'], $qry);
if (mysqli_num_rows($res)) {
	$row = mysqli_fetch_row($res);
	
	$data['route_id'] = $row[0];
	$data['name'] = $row[1].' ('.$cfg_site_prefix.$row[0].')';
	
	$qry = "SELECT `time`, `value`, `smoothed`, `level_of_service`, `freeflow` FROM `route_history` WHERE `route_id` = '".$row[0]."' ORDER BY `time` ASC";
	$res = mysqli_query($db['link'], $qry);
	if (mysqli_num_rows($res)) {
		while ($row = mysqli_fetch_row($res)) {
			//months are 0-based in Google Charts
			$data['values'][] = array(date('Y',$row[0]), 
			date('m',$row[0])-1, 
			date('d',$row[0]), 
			date('H',$row[0]), 
			date('i',$row[0]), 
			date('s',$row[0]), 
			round($row[1]/60,2), 
			floor($row[1]/60).':'.str_pad($row[1]%60, 2, '0', STR_PAD_LEFT), 
			round($row[2]/60), 
			round($row[2]/60).':'.str_pad($row[2]%60, 2, '0', STR_PAD_LEFT), 
			(int) $row[3], 
			round($row[4]/60), 
			floor($row[4]/60).':'.str_pad($row[4]%60, 2, '0', STR_PAD_LEFT));
		}
	}
}
echo json_encode($data);
?>