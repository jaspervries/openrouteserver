openrouteserver - Open source NDW route configurator en server

0. Inhoud
=========

1. Over
2. Systeemvereisten
3. Installatie
4. Gebruik
5. Licentie
6. Auteurs



1. Over
=======

openrouteserver maakt het mogelijk om NDW reistijden van losse trajecten
op te tellen om de samengestelde reistijd van een route te berekenen.
Onder bepaalde voorwaarden worden ontbrekende segmenten aangevuld en bij
het ontbreken van gegevens wordt de laatst bekende reistijd van een 
route enkele minuten herhaald.

openrouteserver beschikt over een interface om routes te configureren en
de reistijd van de laatste periode te tonen in een grafiek.



2. Systeemvereisten
===================

- webserver, bijvoorbeeld Apache
- PHP
- MySQL



3. Installatie
==============

1. Plaats de scripts in een map naar keuze, bijvoorbeeld
   /var/www/openrouteserver
2. Maak een bestand config.cfg.php. Gebruik config.cfg.php.example als
   voorbeeld. Vul de juiste databasegegevens en pull-URL van de NDW
   datastromen in.
3. Voer het script install.php uit om de MySQL database en tabellen aan 
   te maken
4. Voer het script updatemst.php uit om de laatste measurementsitetable
   binnen te halen. Voer dit script regelmatig uit om bij te werken 
   naar de laatste versie.
5. Open het bestand openrouteserver en vul bij INSTALL_DIR het juiste
   pad naar openrouteserver in. Installeer dit bestand als service
   binnen het besturingssysteem, zie 3.1.
6. Ontsluit /gui via de webserver.
7. Start de openrouteserver service.


3.1 Instellen van Linux service
-------------------------------

1. Kopieer het bestand openrouteserver naar /etc/init.d
2. chmod naar 775 en chown naar root:root
3. Voer het volgende commando uit om de service te installeren:
   Red Hat based: chkconfig --add openrouteserver
   Debian based: update-rc.d openrouteserver defaults



4. Gebruik
==========

Ga met een webbrowser naar de via de webserver ontsloten map /gui/admin.
Hier kunnen routes worden samengesteld uit verschillende segmenten. In 
de routeconfiguratie kan ieder segmend vermenigvuldigd worden met een 
zelfgekozen factor en kan een vaste waarde bij het segment worden 
opgeteld. Ook kan de totale route worden vermenigvuldigd met een factor
of kan er een vaste waarde bij de totale route worden opgeteld.

De via de webserver ontsloten map /gui toont nu de geconfigureerde
routes en de actuele reistijd. Klik op de naam van een route om een
grafiek te tonen van het afgelopen reistijdverloop.



5. Licentie
===========

openrouteserver is beschikbaar onder de voorwaarden van de GNU General
Public License versie 2 of hoger. openrouteserver kan vrij worden 
gebruikt zolang aan de in de voorwaarden gestelde vereisten worden
voldaan.


openrouteserver - Open source NDW route configurator en server
Copyright (C) openrouteserver ontwikkelaars

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.



6. Auteurs
==========

openrouteserver is een idee van Jasper Vries.
