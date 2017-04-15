<?php
/*
*    openrouteserver - Open source NDW route configurator en server
*    Copyright (C) 2014,2017 Jasper Vries
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
<title>Open Route Server - Route bewerken</title>
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-2.1.0.min.js"></script>
<script type="text/javascript" language="javascript" src="https://code.jquery.com/ui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" language="javascript" src="editroute.js"></script>
<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<?php
//connect database
include('../../config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

//set defaults
$insertnew = TRUE;
$insertcomplete = FALSE;
$row['name'] = '';
$row['disabled'] = '0';
$row['multiply'] = '1';
$row['add'] = '0';
$segments = array();
//overload stored values
$qry = "SELECT `name`, `disabled`, `multiply`, `add` FROM `routes` WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'";
$res = mysqli_query($db['link'], $qry);
if (mysqli_num_rows($res)) {
	$row = mysqli_fetch_assoc($res);
	if ($_GET['action'] != 'copy') {
		//new entry
		$originalname = $row['name'];
		$insertnew = FALSE;
	}
	//get segments
	if (empty($_POST)) {
		$qry = "SELECT `route_mapping`.`segment_id`, `route_mapping`.`multiply`, `route_mapping`.`add`, `segments`.`length` 
		FROM `route_mapping` 
		LEFT JOIN `segments` ON `route_mapping`.`segment_id` = `segments`.`segment_id` 
		WHERE `route_mapping`.`route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'";
		$res = mysqli_query($db['link'], $qry);
		if (mysqli_num_rows($res)) {
			while ($seg = mysqli_fetch_row($res)) {
				$segments[] = array('id' => $seg[0],
									'multiply' => $seg[1],
									'add' => $seg[2],
									'length' => $seg[3]
				);
			}
		}
	}
}
if (!empty($_POST)) {
	//overload posted values and check for errors
	if (!empty($_POST['routename'])) $row['name'] = $_POST['routename'];
	else $error['routename'] = TRUE;
	if ($_POST['routedisabled'] == 'true') $row['disabled'] = '1';
	else $row['disabled'] = '0';
	$_POST['routemultiply'] = str_replace(',', '.', $_POST['routemultiply']);
	if (is_numeric($_POST['routemultiply'])) $row['multiply'] = $_POST['routemultiply'];
	else $error['routemultiply'] = TRUE;
	$_POST['routeadd'] = str_replace(',', '.', $_POST['routeadd']);
	if (is_numeric($_POST['routeadd'])) $row['add'] = $_POST['routeadd'];
	else $error['routeadd'] = TRUE;
	//process segments
	if (!empty($_POST['segment_id'])) {
		foreach ($_POST['segment_id'] as $num => $segment) {
			//check for errors
			$_POST['segment_multiply'][$num] = str_replace(',', '.', $_POST['segment_multiply'][$num]);
			if (!is_numeric($_POST['segment_multiply'][$num])) $error['segmentmultiply'] = TRUE;
			$_POST['segment_add'][$num] = str_replace(',', '.', $_POST['segment_add'][$num]);
			if (!is_numeric($_POST['segment_add'][$num])) $error['segmentadd'] = TRUE;
			//build array for display
			$segments[] = array('id' => $segment,
								'multiply' => $_POST['segment_multiply'][$num],
								'add' => $_POST['segment_add'][$num],
								'length' => $_POST['segment_length'][$num]
			);
		}
	}
	else $error['nosegments'] = TRUE;
	//if no errors, store/update entry
	if (empty($error)) {
		//insert query
		if ($insertnew == TRUE) {
			$qry = "INSERT INTO `routes` 
			SET `route_id` = NULL,
			`name` = '".mysqli_real_escape_string($db['link'], $_POST['routename'])."',
			`disabled` = '".$row['disabled']."',
			`multiply` = '".$row['multiply']."',
			`add` = '".$row['add']."'";
			$insertcomplete = mysqli_query($db['link'], $qry);
			$_GET['routeid'] = mysqli_insert_id($db['link']);
		}
		//update query
		else {
			$qry = "UPDATE `routes` 
			SET `name` = '".mysqli_real_escape_string($db['link'], $_POST['routename'])."',
			`disabled` = '".$row['disabled']."',
			`multiply` = '".$row['multiply']."',
			`add` = '".$row['add']."'
			WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'";
			$insertcomplete = mysqli_query($db['link'], $qry);
		}
		if ($insertcomplete == FALSE) {
			$error['query'] = TRUE;
		}
		else {
			$check_segments = array();
			//segments, add/update
			foreach ($_POST['segment_id'] as $num => $segment) {
				$check_segments[] = '\''.mysqli_real_escape_string($db['link'], $segment).'\'';
				$qry = "INSERT INTO `route_mapping` 
				SET `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."',
				`segment_id` = '".mysqli_real_escape_string($db['link'], $segment)."',
				`multiply` = '".mysqli_real_escape_string($db['link'], $_POST['segment_multiply'][$num])."',
				`add` = '".mysqli_real_escape_string($db['link'], $_POST['segment_add'][$num])."'
				ON DUPLICATE KEY UPDATE
				`multiply` = '".mysqli_real_escape_string($db['link'], $_POST['segment_multiply'][$num])."',
				`add` = '".mysqli_real_escape_string($db['link'], $_POST['segment_add'][$num])."'";
				mysqli_query($db['link'], $qry);
			}
			//segments, remove old
			$qry = "DELETE FROM `route_mapping` 
			WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'
			AND `segment_id` NOT IN (".implode(',', $check_segments).")";
			mysqli_query($db['link'], $qry);
		}
	}
}

if ($insertcomplete == FALSE) {
	//title
	echo $insertnew ? '<h1>Nieuwe Route</h1>' : '<h1>Route <em>'.htmlspecialchars($originalname).'</em> bewerken</h1>';
	?>
	<?php
	if ($error['query'] == TRUE) 
		echo '<p class="error">Kan niet opslaan in database. Probeer het later nogmaals als het probleem zich blijft voordoen.</p>';
	?>
	<form method="post">
	<fieldset>
	<legend>Algemeen</legend>
	<?php
	if ($error['routename'] == TRUE) 
		echo '<p class="error">Vul een naam voor deze route in.</p>';
	?>
	<table>
	<tr><td><label for="form_routename">Routenaam:</label></td><td><input type="text" name="routename" id="form_routename" value="<?php echo htmlspecialchars($row['name']); ?>"></td></tr>
	<tr><td><label for="form_routedisabled">Uitgeschakeld:</label></td><td><select name="routedisabled" id="form_routedisabled"><option value="false">Nee</option><option value="true"<?php if ($row['disabled'] == '1') echo ' selected="selected"'; ?>>Ja</option></select></td></tr>
	</table>
	</fieldset>
	
	<fieldset>
	<legend>Reistijdaanpassing op routeniveau</legend>
	<?php
	if ($error['routemultiply'] == TRUE) 
		echo '<p class="error">De vermenigvuldigingswaarde moet een getal zijn. Laat de standaardwaarde 1 staan om geen gebruik te maken van deze optie.</p>';
	?>
	<?php
	if ($error['routeadd'] == TRUE) 
		echo '<p class="error">De optelwaarde moet een getal zijn. Laat de standaardwaarde 0 staan om geen gebruik te maken van deze optie.</p>';
	?>
	<table>
	<tr><td><label for="form_routemultiply">Vermenigvuldigen met:</label></td><td><input type="text" name="routemultiply" id="form_routemultiply" value="<?php echo str_replace('.', ',', $row['multiply']); ?>"></td></tr>
	<tr><td><label for="form_routeadd">En dan erbij optellen:</label></td><td><input type="text" name="routeadd" id="form_routeadd" value="<?php echo str_replace('.', ',', $row['add']); ?>"> minuten</td></tr>
	</table>
	</fieldset>
	
	<fieldset>
	<legend>Segmenten</legend>
	<span id="addsegment">[A] Segment toevoegen</span>
    <?php
	if ($error['nosegments'] == TRUE) 
		echo '<p class="error">Een route moet minimaal &eacute;&eacute;n segment bevatten.</p>';
	?>
	<?php
	if ($error['segmentmultiply'] == TRUE) 
		echo '<p class="error">De vermenigvuldigingswaarde moet een getal zijn. Laat de standaardwaarde 1 staan om geen gebruik te maken van deze optie.</p>';
	?>
	<?php
	if ($error['segmentadd'] == TRUE) 
		echo '<p class="error">De optelwaarde moet een getal zijn. Laat de standaardwaarde 0 staan om geen gebruik te maken van deze optie.</p>';
	?>
	<table id="selectedsegments">
	<thead>
	<tr><th></th><th>ID</th><th>Lengte</th><th>Vermenigvuldigen</th><th>Optellen</th></tr>
	</thead>
	<tbody>
    <?php
	foreach ($segments as $segment) {
		echo '<tr><td><span class="removerow">[x]</span></td>
		<td><input type="hidden" name="segment_id[]" value="'.$segment['id'].'">'.$segment['id'].'</td>
		<td><input type="hidden" name="segment_length[]" value="'.$segment['length'].'">'.$segment['length'].'</td>
		<td><input type="text" name="segment_multiply[]" value="'.str_replace('.', ',', $segment['multiply']).'"></td>
		<td><input type="text" name="segment_add[]" value="'.str_replace('.', ',', $segment['add']).'"></td></tr>';
	}
	?>
	</tbody>
	</table>
	</fieldset>
	
	<input type="submit" value="Opslaan"> <a href="index.php">Annuleren</a>
	</form>
    
    <div id="addsegmentdialog">
    	<form id="segmentsearch">
    	<input type="text" id="segmentfind"> <input type="submit" value="Zoeken">
        </form>
        <div id="segmentresult">
        </div>
    </div>
	<?php 
}
else {
	?>
    <h1>Route <em><?php echo htmlspecialchars($_POST['routename']); ?></em> opgeslagen</h1>
    <ul>
    	<li><a href="editroute.php">Een nieuwe route toevoegen</a></li>
        <li><a href="index.php">Terug naar overzicht</a></li>
    </ul>
    <?php
}
?>
</body>
</html>