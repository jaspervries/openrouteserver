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

if (strlen($_GET['q']) >= 5) {
	//connect database
	include('../../config.cfg.php');
	$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);
	
	$qry = "SELECT `segment_id`, `length`, `class`, `name` FROM `segments` WHERE `segment_id` LIKE '%".mysqli_real_escape_string($db['link'], $_GET['q'])."%' ORDER BY `segment_id`";
	$res = mysqli_query($db['link'], $qry);
	if (mysqli_num_rows($res)) {
		?>
		<table id="segmentresult">
		<thead>
		<tr><th></th><th>ID</th><th>Lengte</th><th>Klasse</th><th>Naam</th></tr>
		</thead>
		<tbody>
		<?php
		while ($row = mysqli_fetch_row($res)) {
			echo '<tr><td><span class="selectsegment">[A]</span></td><td class="selectsegment_id">'.htmlspecialchars($row[0]).'</td><td class="selectsegment_length">'.$row[1].'</td><td class="selectsegment_class">'.$row[2].'</td><td>'.htmlspecialchars($row[3]).'</td></tr>';
		}
		?>
		</tbody>
		</table>
		<?php
	}
	else {
		echo '<p class="warning">Geen resultaten. Probeer een ander zoekwoord.</p>';
	}
}
else {
	echo '<p class="warning">Zoekwoord moet uit minstens 5 tekens bestaan.</p>';
}