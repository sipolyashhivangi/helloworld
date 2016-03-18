#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

cd  /var/www/flexscore/scripts/scorechange/
echo "Started - `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/scorecalc.log
echo "Completed - `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log

cd /var/www/flexscore/scripts/networth/
echo "Started - `date`" >> /home/ubuntu/SCRIPTS/log/networth.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/networth.log
echo "Completed - `date`" >> /home/ubuntu/SCRIPTS/log/networth.log

cd /home/ubuntu/SCRIPTS
echo "Started - `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log
wget -O /home/ubuntu/SCRIPTS/log/calcreports.log  https://www.flexscore.com/service/api/calcreports?accTok=$token > /dev/null 2>&1
echo "Completed - `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log

echo -e "Calculation of Networth/ScoreChange/Analytics Completed  \n - FlexScore TEAM"."\n" | mail -s "Production Server - Report - `date`"  "ganesh.m@truglobal.com"

