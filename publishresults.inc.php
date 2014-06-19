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

write_log('publish results', $publicationtime);

//publish results as HTML table
$qry_routes = "SELECT `route_history`.`route_id`, `route_history`.`time`, `route_history`.`value`, `route_history`.`level_of_service`, `routes`.`name` FROM `route_history`
INNER JOIN (SELECT `route_id`, MAX(`time`) AS `mtime` FROM `route_history`
	GROUP BY (`route_id`)) AS `t1`
	ON `t1`.`route_id` = `route_history`.`route_id` AND `t1`.`mtime` = `route_history`.`time`
LEFT JOIN `routes`
	ON `routes`.`route_id` = `route_history`.`route_id`
WHERE `routes`.`disabled` = 0
ORDER BY `routes`.`name`";
$res_routes = mysqli_query($db['link'], $qry_routes);
if (mysqli_num_rows($res_routes)) {
   	$html = '<table>
    <thead>
    <tr><th>Naam</th><th>Reistijd [min]</th></tr>
    </thead>
    <tbody>';
	while ($row_routes = mysqli_fetch_row($res_routes)) {
		$html.= '<tr>
		<td>
		<a onclick="showChart('.$row_routes[0].')" title="TDS01_ORS'.$row_routes[0].'">
		'.htmlspecialchars($row_routes[4]).'
		</a>';
		if ($row_routes[1] > (time() - 60*5)) {
			$html.= '</td><td style="color:'.$cfg_class_colour[$row_routes[3]].';background-color:#333;">';
		}
		else {
			$html.= '</td><td style="color:'.$cfg_class_colour[$row_routes[3]].';background-color:#999;">';
		}
		$html.= floor($row_routes[2]/60).':'.str_pad($row_routes[2]%60, 2, '0', STR_PAD_LEFT).'
		</td>
		<tr>';
	}
	$html.= '</tbody>
    </table>';
}
else {
	$html = '<p>Geen routes geconfigureerd.</p>';
}
//write file
$hdl = fopen('gui/routes.html', 'w');
fwrite($hdl, $html);
fclose($hdl);

//set last update time
$hdl = fopen('gui/lastupdate.html', 'w');
fwrite($hdl, time());
fclose($hdl);
?>