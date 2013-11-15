cd /home/prj/tbautf8
rm ./log/*.log
php exectbs.php cachetb > ./log/log-today-cache.log
php exectbs.php > ./log/log-today-sign.log
