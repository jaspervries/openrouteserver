#! /bin/sh

sleep 10
cd /var/www/server
date >> reboot.log
nohup php -f mainloop.php > /dev/null 2>&1 &
