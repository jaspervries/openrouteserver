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

//function to write log file
if (!function_exists('write_log')) { function write_log ($string = '', $NDWtime = '') {
	if (!empty($string)) {
		if (file_exists('log.txt')) {
			$hdl = fopen('log.txt', 'r');
			$oldlog = fread($hdl, 5000000);
			fclose($hdl);
		}
		else $oldlog = '';
		$log = date('Y-m-d H:i:s')."\t";
		if (!empty($NDWtime)) $log .= date('H:i', $NDWtime);
		$log .= "\t".$string."\r\n" . $oldlog;
		$hdl = fopen('log.txt', 'w');
		fwrite($hdl, $log);
		fclose($hdl);
	}
}}
?>