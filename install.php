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

include('config.cfg.php');

$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass']);


$qry = "CREATE DATABASE `".$cfg_db['db']."`
COLLATE 'latin1_general_ci'";
if (mysqli_query($db['link'], $qry)) echo '<p>database created</p>';
else echo '<p>did not create database</p>';

$db['link'] = mysqli_connect($cfg_db['host'], $cfg_db['user'], $cfg_db['pass'], $cfg_db['db']);

$qry = "CREATE TABLE `route_history`
(
	`route_id` INT UNSIGNED NOT NULL,
	`time` INT UNSIGNED NOT NULL,
	`value`  INT UNSIGNED NOT NULL,
	`filtered`  INT UNSIGNED NOT NULL DEFAULT 0,
	`level_of_service` TINYINT UNSIGNED NOT NULL DEFAULT 0,
	CONSTRAINT `history` PRIMARY KEY (`route_id`, `time`)
)
ENGINE `MyISAM`,
CHARACTER SET 'latin1', 
COLLATE 'latin1_general_ci'";
if (mysqli_query($db['link'], $qry)) echo '<p>table `route_history` created</p>';
else echo '<p>did not create table `route_history`</p>';
echo mysqli_error($db['link']);

$qry = "CREATE TABLE `segments`
(
	`segment_id` VARCHAR(255) NOT NULL PRIMARY KEY,
	`name` VARCHAR(255) NULL,
	`length` INT UNSIGNED NOT NULL,
	`class` ENUM('R','P','G') NOT NULL DEFAULT 'R',
	`coordinates` MEDIUMTEXT NULL
)
ENGINE `MyISAM`,
CHARACTER SET 'latin1', 
COLLATE 'latin1_general_ci'";
if (mysqli_query($db['link'], $qry)) echo '<p>table `segments` created</p>';
else echo '<p>did not create table `segments`</p>';
echo mysqli_error($db['link']);

$qry = "CREATE TABLE `routes`
(
	`route_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(255) NOT NULL,
	`disabled` BOOLEAN NOT NULL DEFAULT 0,
	`coordinates` MEDIUMTEXT NULL,
	`multiply` FLOAT SIGNED NOT NULL DEFAULT 1,
	`add` FLOAT SIGNED NOT NULL DEFAULT 0
)
ENGINE `MyISAM`,
CHARACTER SET 'latin1', 
COLLATE 'latin1_general_ci'";
if (mysqli_query($db['link'], $qry)) echo '<p>table `routes` created</p>';
else echo '<p>did not create table `routes`</p>';
echo mysqli_error($db['link']);

$qry = "CREATE TABLE `route_mapping`
(
	`route_id` INT UNSIGNED NOT NULL,
	`segment_id` VARCHAR(255) NOT NULL,
	`multiply` FLOAT SIGNED NOT NULL DEFAULT 1,
	`add` FLOAT SIGNED NOT NULL DEFAULT 0,
	CONSTRAINT `mapping` PRIMARY KEY (`route_id`, `segment_id`)
)
ENGINE `MyISAM`,
CHARACTER SET 'latin1', 
COLLATE 'latin1_general_ci'";
if (mysqli_query($db['link'], $qry)) echo '<p>table `route_mapping` created</p>';
else echo '<p>did not create table `route_mapping`</p>';
echo mysqli_error($db['link']);

?>