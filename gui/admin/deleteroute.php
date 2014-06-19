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
<title>Open Route Server - Route verwijderen</title>
<link rel="stylesheet" type="text/css" href="../style.css">
</head>
<body>
<?php
//connect database
include('../../config.cfg.php');
$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

$insertcomplete = FALSE;

//get route name
$qry_routes = "SELECT `name` FROM `routes` WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'";
$res_routes = mysqli_query($db['link'], $qry_routes);
if (mysqli_num_rows($res_routes)) {
	$row = mysqli_fetch_assoc($res_routes);
}
if (!empty($_POST)) {
	//if no errors, store/update entry
	if ($_POST['deleteconfirm'] == 'true') {
		
		$qry = "DELETE FROM `route_mapping` 
		WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'";
		if (mysqli_query($db['link'], $qry)) {
			$qry = "DELETE FROM `routes` 
			WHERE `route_id` = '".mysqli_real_escape_string($db['link'], $_GET['routeid'])."'";
			$insertcomplete = mysqli_query($db['link'], $qry);
		if ($insertcomplete == FALSE) $error['query'] = TRUE;
		}
	}
	else $error['deleteconfirm'] = TRUE;
}

if ($insertcomplete == FALSE) {
	//title
	echo '<h1>Route <em>'.htmlspecialchars($row['name']).'</em> verwijderen</h1>';
	?>
	<?php
	if ($error['query'] == TRUE) 
		echo '<p class="error">Kan niet opslaan in database. Probeer het later nogmaals als het probleem zich blijft voordoen.</p>';
	?>
	<form method="post">
	<fieldset>
	<legend>Bevestig verwijderen</legend>
    <?php
	if ($error['deleteconfirm'] == TRUE) 
		echo '<p class="error">Schakel het selectievakje in om het verwijderen te bevestigen.</p>';
	?>
	<p>Deze actie kan niet ongedaan gemaakt worden.</p>
    <p><input type="checkbox" name="deleteconfirm" id="deleteconfirm" value="true"> <label for="deleteconfirm">Route verwijderen</label></p>
	</fieldset>
    
	<input type="submit" name="submitbutton" value="Verwijderen"> <a href="index.php">Annuleren</a>
	</form>
	<?php 
}
else {
	?>
    <h1>Route <em><?php echo htmlspecialchars($row['name']); ?></em> verwijderd</h1>
    <ul>
    	<li><a href="editroute.php">Een nieuwe route toevoegen</a></li>
        <li><a href="index.php">Terug naar overzicht</a></li>
    </ul>
    <?php
}
?>
</body>
</html>