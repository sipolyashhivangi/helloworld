#!/bin/bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

cd  /sftp/ftpce/incoming
/usr/bin/perl /var/www/dev/scripts/cashedge/ncrawler.pl >/dev/null 2>&1

cd /var/www/dev/scripts/batchfiles
chown www-data:www-data *.csv

cd /var/www/dev/scripts/cashedge
wget -o processbatchfiles.log https://dev.flexscore.com/dev/service/api/processbatchfiles?accTok=$token

exit
