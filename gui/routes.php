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

//connect database
include('../config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);
include('../segmentclasses.cfg.php');

$qry_routes = "SELECT `route_history`.`route_id`, `route_history`.`time`, `route_history`.`value`, `route_history`.`level_of_service`, `routes`.`name` FROM `route_history`
INNER JOIN (SELECT `route_id`, MAX(`time`) AS `mtime` FROM `route_history`
	GROUP BY (`route_id`)) AS `t1`
	ON `t1`.`route_id` = `route_history`.`route_id` AND `t1`.`mtime` = `route_history`.`time`
LEFT JOIN `routes`
	ON `routes`.`route_id` = `route_history`.`route_id`
WHERE `routes`.`disabled` = 0";
$res_routes = mysqli_query($db['link'], $qry_routes);
if (mysqli_num_rows($res_routes)) {
	?>
    <table>
    <thead>
    <tr><th>Naam</th><th>Reistijd [min]</th></tr>
    </thead>
    <tbody>
    <?php
	while ($row_routes = mysqli_fetch_row($res_routes)) {
		echo '<tr>';
		echo '<td>';
		echo '<a onclick="showChart('.$row_routes[0].')" title="'.$cfg_site_prefix.$row_routes[0].'">';
		echo htmlspecialchars($row_routes[4]);
		echo '</a>';
		if ($row_routes[1] > (time() - 60*5)) {
			echo '</td><td style="color:'.$cfg_class_colour[$row_routes[3]].';background-color:#333;">';
		}
		else {
			echo '</td><td style="color:'.$cfg_class_colour[$row_routes[3]].';background-color:#999;">';
		}
		echo floor($row_routes[2]/60).':'.str_pad($row_routes[2]%60, 2, '0', STR_PAD_LEFT);
		echo '</td>';
		echo '<tr>';
	}
	?>
    </tbody>
    </table>
    <?php
}
?>