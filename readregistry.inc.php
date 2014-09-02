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

/*
 * REGISTRY DEFAULTS
*/

$reg['ema_alpha'] = 0.75;
$reg['ema_alpha_freeflow'] = 0.05;

/*
 * OVERLOAD WITH DATABASE VALUES
*/

$qry = "SELECT `key`, `value` FROM `registry`";
$res = mysqli_query($db['link'], $qry);
while ($row = mysqli_fetch_row($res)) {
	$reg[$row[0]] = $row[1];
}
?>