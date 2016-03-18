#!/usr/bin/env bash
datenow=`date`

# Staging Configs - checkk contains the count of actionstep instances currently running
checkk=`ps aux | grep "actionsteps" | grep -v "grep" | wc -l`
if [ $checkk -eq 0 ]
then
        nohup /bin/bash /home/ubuntu/SCRIPTS/actionsteps.sh > /dev/null 2>&1 &
        echo "$datenow Restarted Action Steps" >>  /home/ubuntu/SCRIPTS/health.log
else
        echo "$datenow Running Action Steps" >>  /home/ubuntu/SCRIPTS/health.log
fi

# Staging Configs - checkm contains the count of monte carlo instances currently running
checkm=`ps aux | grep "montecarlo" | grep -v "grep" | wc -l`
if [ $checkm -eq 0 ]
then
        nohup /bin/bash /home/ubuntu/SCRIPTS/montecarlo.sh > /dev/null 2>&1 &
        echo "$datenow Restarted Monte Carlo" >>  /home/ubuntu/SCRIPTS/montecarlo.log
else
        echo "$datenow Running Monte Carlo" >>  /home/ubuntu/SCRIPTS/montecarlo.log
fi

# Staging Configs - checkpy contains the PIDs for the mail instances.
#                   checkp contains the count of mail instances currently running
checkpy=`ps aux | grep "getmailandsend.py" | grep -v "grep" | awk ' { print $2 }'`
checkp=`ps aux | grep "getmailandsend.py" | grep -v "grep" | awk ' { print $2 }' | wc -w`

if [ $checkp -gt 0 ]
then
        sudo kill -9 $checkpy
        nohup /usr/bin/python /var/www/staging/scripts/python-mail/getmailandsend.py > /dev/null 2>&1 &
        nohup /usr/bin/python /var/www/production/scripts/python-mail/getmailandsend.py > /dev/null 2>&1 &
        echo "$datenow Killed and Restarted Mailer Scripts" >>  /home/ubuntu/SCRIPTS/health.log
else
        nohup /usr/bin/python /var/www/staging/scripts/python-mail/getmailandsend.py > /dev/null 2>&1 &
        nohup /usr/bin/python /var/www/production/scripts/python-mail/getmailandsend.py > /dev/null 2>&1 &
        echo "$datenow Restarted Mailer Scripts" >>  /home/ubuntu/SCRIPTS/health.log
fi