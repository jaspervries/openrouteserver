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
?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Open Route Server - Routelijst</title>
<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<a href="editroute.php">[A] Nieuwe Route</a>
<?php
//connect database
include('../../config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

$qry_routes = "SELECT `route_id`, `name` FROM `routes`";
$res_routes = mysqli_query($db['link'], $qry_routes);
if (mysqli_num_rows($res_routes)) {
	?>
    <table>
    <thead>
    <tr><th></th><th>ID</th><th>Naam</th></tr>
    </thead>
    <tbody>
    <?php
	while ($row_routes = mysqli_fetch_row($res_routes)) {
		echo '<tr>';
		echo '<td>';
		echo '<a href="editroute.php?routeid='.$row_routes[0].'" title="route bewerken">[E]</a> <a href="editroute.php?routeid='.$row_routes[0].'&amp;action=copy" title="route kopieren">[C]</a> <a href="deleteroute.php?routeid='.$row_routes[0].'" title="route verwijderen">[X]</a>';
		echo '</td><td>';
		echo 'TDS01_ORS'.$row_routes[0];
		echo '</td><td>';
		echo htmlspecialchars($row_routes[1]);
		echo '</td>';
		echo '<tr>';
	}
	?>
    </tbody>
    </table>
    <?php
}
?>
</body>
</html>