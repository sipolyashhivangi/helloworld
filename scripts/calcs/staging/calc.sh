#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

cd  /var/www/staging/scripts/scorechange/
date >> /home/ubuntu/SCRIPTS/log/scorecalc.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/scorecalc.log
echo "Completed - Staging `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log

cd /var/www/staging/scripts/networth/
date >> /home/ubuntu/SCRIPTS/log/networth.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/networth.log
echo "Completed - Staging `date`" >> /home/ubuntu/SCRIPTS/log/networth.log

cd /home/ubuntu/SCRIPTS
date >> /home/ubuntu/SCRIPTS/log/calcreports.log
wget -O /home/ubuntu/SCRIPTS/log/calcreports.log  https://staging.flexscore.com/staging/service/api/calcreports?accTok=$token > /dev/null 2>&1
echo "Completed - Staging `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log

echo  "Calculation of Networth/ScoreChange/Analytics Completed Staging-Staging  \n - FlexScore TEAM"."\n" | mail -s "Staging Server - Report - `date`"  "ganesh.m@flexscore.com"

# Below SCRIPTS for  Staging/Production

cd  /var/www/production/scripts/scorechange/
date >> /home/ubuntu/SCRIPTS/log/scorecalc.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/scorecalc.log
echo "Completed - Production  `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log

cd /var/www/production/scripts/networth/
date >> /home/ubuntu/SCRIPTS/log/networth.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/networth.log
echo "Completed - Production `date`" >> /home/ubuntu/SCRIPTS/log/networth.log

cd /home/ubuntu/SCRIPTS
date >> /home/ubuntu/SCRIPTS/log/calcreports.log
wget -O /home/ubuntu/SCRIPTS/log/calcreports.log  https://staging.flexscore.com/production/service/api/calcreports?accTok=$token > /dev/null 2>&1
echo "Completed - Production `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log

echo  "Calculation of Networth/ScoreChange/Analytics Completed Staging-Production   \n - FlexScore TEAM"."\n" | mail -s "Staging Server - Report - `date`"  "ganesh.m@flexscore.com"


# Below SCRIPTS for  Staging/Mobile

cd  /var/www/mobile/scripts/scorechange/
date >> /home/ubuntu/SCRIPTS/log/scorecalc.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/scorecalc.log
echo "Completed - Mobile  `date`" >> /home/ubuntu/SCRIPTS/log/scorecalc.log

cd /var/www/mobile/scripts/networth/
date >> /home/ubuntu/SCRIPTS/log/networth.log
php calculate.php >> /home/ubuntu/SCRIPTS/log/networth.log
echo "Completed - Mobile `date`" >> /home/ubuntu/SCRIPTS/log/networth.log

cd /home/ubuntu/SCRIPTS
date >> /home/ubuntu/SCRIPTS/log/calcreports.log
wget -O /home/ubuntu/SCRIPTS/log/calcreports.log  https://staging.flexscore.com/mobile/service/api/calcreports?accTok=$token > /dev/null 2>&1
echo "Completed - Mobile `date`" >> /home/ubuntu/SCRIPTS/log/calcreports.log

echo  "Calculation of Networth/ScoreChange/Analytics Completed Staging-Mobile   \n - FlexScore TEAM"."\n" | mail -s "Staging Server - Report - `date`"  "ganesh.m@flexscore.com"
