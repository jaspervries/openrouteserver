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

//LOS 0 must not be defined here
$cfg_class['R'][1] = 30;
$cfg_class['R'][2] = 60;
$cfg_class['R'][3] = 90;
$cfg_class['P'][1] = 20;
$cfg_class['P'][2] = 40;
$cfg_class['P'][3] = 60;
$cfg_class['G'][1] = 10;
$cfg_class['G'][2] = 20;
$cfg_class['G'][3] = 30;
//colour for LOS 0 must be defined here
$cfg_class_colour[0] = '#CC0000';
$cfg_class_colour[1] = '#FF9900';
$cfg_class_colour[2] = '#FFFF00';
$cfg_class_colour[3] = '#33CC00';
//automatically apply specific class when prefixed
//unknown defaults to P class
$cfg_class_G = array ('GEO01_SRE', 'GEO02_GDH', 'GEO02_KAN', 'GRT01', 'GUT01', 'SRR01');
$cfg_class_R = array ('GEO01_R_RWS', 'GEO01_Z_RWS', 'GEO02_N_RWS', 'GEO02_R_RWS', 'GEO02_Z_RWS', 'RWS01', 'GEO03_D4T-RWS');
?>