#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

while :
    do
    wget -O /home/ubuntu/SCRIPTS/act.log https://www.flexscore.com/service/api/steps?accTok=$token
    echo "For production" >> /home/ubuntu/SCRIPTS/act.log
    date >> /home/ubuntu/SCRIPTS/act.log
    sleep 1
done

