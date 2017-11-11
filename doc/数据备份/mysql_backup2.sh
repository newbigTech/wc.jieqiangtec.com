#!/bin/bash
#设置mysql备份目录
folder=/mysql_backup
cd $folder
day=`date +%Y%m%d`
now2=`date +%H%M%S`
#rm -rf $day
mkdir $day
cd $day
#数据库服务器，一般为localhost
host=localhost
#用户名
user=root
#密码
password=jieqiang520
#要备份的数据库
db_cps=cps
db_shopcenter=shopcenter
db_wc=wc
db_wc2=wc2
db_wd=wd
db_test=test

#数据要保留的天数
days=3
/alidata/server/mysql/bin/mysqldump -h$host -u$user -p$password $db_cps>backup_$db_cps.sql
/alidata/server/mysql/bin/mysqldump -h$host -u$user -p$password $db_shopcenter>backup_$db_shopcenter.sql
/alidata/server/mysql/bin/mysqldump -h$host -u$user -p$password $db_wc>backup_$db_wc.sql
/alidata/server/mysql/bin/mysqldump -h$host -u$user -p$password $db_wc2>backup_$db_wc2.sql
/alidata/server/mysql/bin/mysqldump -h$host -u$user -p$password $db_wd>backup_$db_wd.sql
/alidata/server/mysql/bin/mysqldump -h$host -u$user -p$password $db_test>backup_$db_test.sql

#zip backup.sql.zip backup.sql
tar zcvf backup_$day$now2.sql.tar.gz backup_$db_cps.sql backup_$db_shopcenter.sql backup_$db_wc.sql backup_$db_wc2.sql backup_$db_wd.sql backup_$db_test.sql
rm backup_$db_cps.sql backup_$db_shopcenter.sql backup_$db_wc.sql  backup_$db_wc2.sql backup_$db_wd.sql backup_$db_test.sql
#cd ..
#day=`date -d "$days days ago" +%Y%m%d`
#rm -rf $day
