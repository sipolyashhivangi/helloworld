#!/usr/bin/env bash

result=`wget --no-check-certificate https://staging.flexscore.com:8080`;

if [ $? != 0 ]
then
#Killd process
for session in $(screen -ls | grep -o '[0-9]\{5\}')
do
sudo kill -9 $session
screen -wipe
done

#Restarting Process
cd /var/www/production/service/node
screen -dmS -s nodemon connectionCounter.js

cd /var/www/staging/service/node
screen -dmS -s nodemon connectionCounter.js

cd /var/www/mobile/service/node
screen -dmS -s nodemon connectionCounter.js


echo "`date` Node Killed and Restarted... " >> /home/ubuntu/SCRIPTS/node.log
reset

else
echo "`date` Node Running OK " >> /home/ubuntu/SCRIPTS/node.log
fi

rm /home/ubuntu/SCRIPTS/index.html
