#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

while :
    do
    wget -O /home/ubuntu/SCRIPTS/act.log https://staging.flexscore.com/staging/service/api/steps?accTok=$token
    echo "For staging" >> /home/ubuntu/SCRIPTS/act.log
    date >> /home/ubuntu/SCRIPTS/act.log

    wget -O /home/ubuntu/SCRIPTS/act.log https://staging.flexscore.com/mobile/service/api/steps?accTok=$token
    echo "For mobile" >> /home/ubuntu/SCRIPTS/act.log
    date >> /home/ubuntu/SCRIPTS/act.log

    wget -O /home/ubuntu/SCRIPTS/act.log https://staging.flexscore.com/production/service/api/steps?accTok=$token
    echo "For production" >> /home/ubuntu/SCRIPTS/act.log
    date >> /home/ubuntu/SCRIPTS/act.log
    sleep 1

done

