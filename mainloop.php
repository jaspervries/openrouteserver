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

set_time_limit(0);

//connect database
include('config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

//include log function
include('log.inc.php');
include('segmentclasses.cfg.php');

while(TRUE) {
	//set start time of loop, to calculate sleep later
	$time_mainloop_start = time();
	
	include('getdata.inc.php');
	if ($oldpublicationtime != $publicationtime) {
		//further process only if new data
		include('calculateroutes.inc.php');
		include('publishresults.inc.php');
		include('cleanhistory.inc.php');
	}
	
	//calculate sleep
	//new pull 58 seconds after previous
	$sleep = max(0, ($time_mainloop_start + 58 - time()));
	write_log('sleep: '.$sleep);
	sleep($sleep);
}
?>