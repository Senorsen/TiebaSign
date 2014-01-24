TiebaSign
=========

php script with php (linux command line).


Usage
---
Create file: conn.php, and connect the mysql server to variable: $con .  
Create a table(named tb_user) in a mysql database (such as test);  
This table has four fields: `id`(A_I), `desc`(user description; nickname), `cookies`(at wapp.baidu.com) and `last`.  
Type: int, varchar(255), varchar(1024), varchar(50)  
Save your 'baidu tieba' account info here.  

(See tbs.sql)

Then open the php in cosole mode  
(Manual: )    

`php exectbs.php cachetb` to cache tieba list first, then:   
`php exectbs.php`    

(Auto: )   
/home/tbs/autotbs.sh:   

    php exectbs.php cachetb > ./log/log-today-cache.log
    php exectbs.php > ./log/log-today-sign.log


crontab -e:   

    * */4 * * * 


That's all.  
