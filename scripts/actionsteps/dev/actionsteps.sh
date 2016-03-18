#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

while :
    do
    
    wget -O /home/ubuntu/SCRIPTS/act.log https://dev.flexscore.com/dev/service/api/steps?accTok=$token
    echo "For /dev " >> /home/ubuntu/SCRIPTS/act.log
    date >> /home/ubuntu/SCRIPTS/act.log
    
    wget -O /home/ubuntu/SCRIPTS/act.log https://dev.flexscore.com/test/service/api/steps?accTok=$token
    echo "For /test " >> /home/ubuntu/SCRIPTS/act.log
    date >> /home/ubuntu/SCRIPTS/act.log
    
    sleep 1

    done

