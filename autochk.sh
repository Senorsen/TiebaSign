cd /home/prj/tbs
rm ./log/log-today-check.log
today=`date +%Y-%m-%d`
php exectbs.php check> ./log/log-today-check.log
cp ./log/log-today-check.log ./log/log-$today"-check.log"
