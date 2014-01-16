cd /home/prj/tbs
rm ./log/log-today-cache.log
today=`date +%Y-%m-%d`
php exectbs.php cachetb > ./log/log-today-cache.log
cp ./log/log-today-cache.log ./log/log-$today"-cache.log"
