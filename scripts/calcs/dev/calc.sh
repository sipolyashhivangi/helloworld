#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

cd  /var/www/dev/scripts/scorechange/
date >> /home/ubuntu/SCRIPTS/log/scorecalc.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/scorecalc.log
echo "Completed - DevBox `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log

cd /var/www/dev/scripts/networth/
date >> /home/ubuntu/SCRIPTS/log/networth.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/networth.log
echo "Completed - DevBox `date`" >> /home/ubuntu/SCRIPTS/log/networth.log

cd /home/ubuntu/SCRIPTS
date >> /home/ubuntu/SCRIPTS/log/calcreports.log
wget -O /home/ubuntu/SCRIPTS/log/calcreports.log  https://dev.flexscore.com/dev/service/api/calcreports?accTok=$token > /dev/null 2>&1
echo "Completed - DevBox `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log

echo  "Calculation of Networth/ScoreChange/Analytics Completed DevBox-Dev  \n - FlexScore TEAM"."\n" | mail -s "Dev Server - Report - `date`"  "ganesh.m@truglobal.com"

cd  /var/www/test/scripts/scorechange/
date >> /home/ubuntu/SCRIPTS/log/scorecalc.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/scorecalc.log
echo "Completed - Test  `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log

cd /var/www/test/scripts/networth/
date >> /home/ubuntu/SCRIPTS/log/networth.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/networth.log
echo "Completed - Test `date`" >> /home/ubuntu/SCRIPTS/log/networth.log

cd /home/ubuntu/SCRIPTS
date >> /home/ubuntu/SCRIPTS/log/calcreports.log
wget -O /home/ubuntu/SCRIPTS/log/calcreports.log  https://dev.flexscore.com/test/service/api/calcreports?accTok=$token > /dev/null 2>&1
echo "Completed - Test `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log

echo  "Calculation of Networth/ScoreChange/Analytics Completed DevBox-Test   \n - FlexScore TEAM"."\n" | mail -s "Dev Server - Report - `date`"  "ganesh.m@truglobal.com"

