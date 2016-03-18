#!/usr/bin/env bash
datenow=`date`

# Production Configs - checkk contains the count of actionstep instances currently running
checkk=`ps aux | grep "actionsteps" | grep -v "grep" | wc -l`
if [ $checkk -eq 0 ]
then
        nohup /bin/bash /home/ubuntu/SCRIPTS/actionsteps.sh > /dev/null 2>&1 &
        echo "$datenow Restarted Action Steps" >>  /home/ubuntu/SCRIPTS/health.log
else
        echo "$datenow Running Action Steps" >>  /home/ubuntu/SCRIPTS/health.log
fi

# Production Configs - checkm contains the count of monte carlo instances currently running
checkm=`ps aux | grep "montecarlo" | grep -v "grep" | wc -l`
if [ $checkm -eq 0 ]
then
        nohup /bin/bash /home/ubuntu/SCRIPTS/montecarlo.sh > /dev/null 2>&1 &
        echo "$datenow Restarted Monte Carlo" >>  /home/ubuntu/SCRIPTS/montecarlo.log
else
        echo "$datenow Running Monte Carlo" >>  /home/ubuntu/SCRIPTS/montecarlo.log
fi

# Production Configs - checkpy contains the PIDs for the mail instances.
#                      checkp contains the count of mail instances currently running
checkpy=`ps aux | grep "getmailandsend.py" | grep -v "grep" | awk ' { print $2 }'`
checkp=`ps aux | grep "getmailandsend.py" | grep -v "grep" | awk ' { print $2 }' | wc -w`
if [ $checkp -gt 0 ]
then
        sudo kill -9 $checkpy
        nohup /usr/bin/python /var/www/flexscore/scripts/python-mail/getmailandsend.py > /dev/null 2>&1 &
        echo "$datenow Killed and Restarted Mailer Scripts" >>  /home/ubuntu/SCRIPTS/health.log
else
        nohup /usr/bin/python /var/www/flexscore/scripts/python-mail/getmailandsend.py > /dev/null 2>&1 &
        echo "$datenow Restarted Mailer Scripts" >>  /home/ubuntu/SCRIPTS/health.log
fi

cpuavg=` w | head -1 `
cpuload=` w | head -1 | awk ' { print $10 }'`

if [ $cpuload -gt 5 ]
then
       echo -e "CPU High Load on Production Server. \n $cpuavg  \n - FlexScore TEAM"."\n" | mail -s "FlexScore CPU high!!!  - `date`"  "alert@flexscore.com"
       echo "$datenow CPU High load $cpuload " >> /home/ubuntu/SCRIPTS/cpu.log
else
       echo "$datenow CPU load $cpuload " >> /home/ubuntu/SCRIPTS/cpu.log
fi
