#!/usr/bin/env bash

result=`wget --no-check-certificate https://dev.flexscore.com:8081`;

if [ $? != 0 ]
then
#Kill process
for session in $(screen -ls | grep -o '[0-9]\{5\}')
do
sudo kill -9 $session
screen -wipe
done

#Restarting Process
cd /var/www/dev/service/node
screen -dmS -s nodemon connectionCounter.js

cd /var/www/test/service/node
screen -dmS -s nodemon connectionCounter.js


echo "`date` Node Killed and Restarted... " >> /home/ubuntu/SCRIPTS/node.log
reset

else
echo "`date` Node Running OK " >> /home/ubuntu/SCRIPTS/node.log
fi

rm /home/ubuntu/SCRIPTS/index.html
