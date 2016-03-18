#!/usr/bin/env bash
token=`echo -n flexSc@re1;l | md5sum|  awk ' { print $1 }'`

while :
    do
        wget -O /home/ubuntu/SCRIPTS/montecarlo.log --no-check-certificate https://staging.flexscore.com/staging/service/api/runmontecarlo?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
        echo "For /staging" >> /home/ubuntu/SCRIPTS/montecarlo.log
        date >> /home/ubuntu/SCRIPTS/montecarlo.log

        wget -O /home/ubuntu/SCRIPTS/montecarlo.log --no-check-certificate https://staging.flexscore.com/production/service/api/runmontecarlo?accTok=flexSc@re1140f81cd544642d52a6c2199926686bc
        echo "For /production" >> /home/ubuntu/SCRIPTS/montecarlo.log
        date >> /home/ubuntu/SCRIPTS/montecarlo.log
    sleep 1
done