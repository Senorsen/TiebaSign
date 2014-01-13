cd /home/prj/tbs
rm ./log/log-today-sign.log
today=`date +%Y-%m-%d`
php exectbs.php > ./log/log-today-sign.log
cp ./log/log-today-sign.log ./log/log.$today".sign.log"
